<?php

/*
 * Convert old mysql code to PDO version
 */

require_once(__DIR__ . "/vendor/autoload.php");

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;


class mysql2pdo
{
    public $path = __DIR__ . "/../";
    public $vars = [];

    public function __construct()
    {

    }

    public function getFiles()
    {
        $files = glob($this->path . "*.php");
        $f = [];

        foreach ($files as $file) {
            $finfo = pathinfo($file);
            $f[] = [
                "path"     => realpath($file),
                "filename" => $finfo['basename'],
            ];

        }
        return $f;


    }

    public function parseFile($file)
    {
        $code = file_get_contents($file);

        $code_statements = explode("\n", $code);

        $code = '<?php' . "\n$code\n" . '?>';

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP5);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }


        $visitor = new class extends NodeVisitorAbstract
        {
            public function leaveNode(Node $node)
            {
                //return NodeTraverser::REMOVE_NODE;
                $fi = "nodes.txt";

                if ($node instanceof Node\Scalar\LNumber) {
                    return new Node\Scalar\String_((string)$node->value);
                }


                if ($node instanceof Node\Expr\ArrayDimFetch) {
                    $this->vars[][$node->var->name] = $node->dim->value;
                }

                if ($node instanceof Node\Expr\FuncCall) {
                    //$this->vars[] = $node->value;

                    $args = [];
                    foreach ($node->args as $arg) {
                        //print_r($arg);
                        //die("DIE:" . $arg->value->value);
                        if (isset($arg->value->value)) {
                            $args[] = $arg->value->value;
                        }
                    }

                    $this->vars[] = [
                        "fn" => [
                            "name" => $node->name->parts[0],
                            "args" => $args
                        ]
                    ];
                }

                if ($node instanceof Node\Scalar\String_) {
                    $this->vars[] = $node->value;
                }

            }
        };

        //Example that can be found in clip.txt
        /*
        $sql = 'UPDATE events SET id_users__memedia='.(int)$_POST['id_users__memedia'].', id_users__changedBy='.getParameter('userid').' WHERE id='.$GET['id'];
        toDatabase($sql);
         */
        $traverser = new NodeTraverser;
        $traverser->addVisitor($visitor);
        $modifiedStmts = $traverser->traverse($ast);

        $newCommand = [];

        $params = [];
        foreach ($visitor->vars as $v) {

            $hasSpace = false;

            if (!is_array($v)) {
                $hasSpace = strpos($v, ' ') !== false;
            }

            if (!is_array($v) && $hasSpace) {
                $newCommand[] = $v;
            }

            if (!is_array($v) && !$hasSpace) {
                $lastVar = $v;
                $newCommand[] = ':' . $v;
                $params[$v] = "?";
            }

            if (is_array($v)) {
                if (isset($v['_POST'])) {
                    $params[$v['_POST']] = '$_POST["' . $v['_POST'] . '"]';
                }
                if (isset($v['_GET'])) {
                    $params[$v['_GET']] = '$_GET["' . $v['_GET'] . '"]';
                }
                if (isset($v['GET'])) {
                    $params[$v['GET']] = '$GET["' . $v['GET'] . '"]';
                }
                if (isset($v['fn'])) {
                    $pos = stripos($v['fn']['name'], 'database');

                    if (!empty($pos)) {
                        //die($v['fn']['name']  "===" . $pos);
                        continue;
                    }
                    $fnCall = $v['fn']['name'] . '(';
                    $fnCall .= implode(',', $v['fn']['args']);
                    $fnCall .= ')';
                    $params[$lastVar] = $fnCall;

                    //$params[$v['GET']] = '$GET["' . $v['GET'] . '"]';
                }

            }


        }

        //Now build the query:
        $sql = implode('', $newCommand);

        //build params:
        $_par = [];
        foreach ($params as $k => $v) {
            $_par[] = "'" . $k . "' => " . $v;
        }

        $params = implode(",\n", $_par);

        $fn = '';
        $fun = '';
        if (stripos($code, "fromDatabase") !== false) {
            $fn = '$db->fromDatabase';
            $fun = 'from';
        }
        if (stripos($code, "toDatabase") !== false) {
            $fn = '$db->toDatabase';
            $fun = 'to';
        }

        $final_output = [];
        $final_output[] = '$sql = "' . $sql . '"';

        if ($fun == 'to') {
            $final_output[] = $fn . '($sql, [' . "\n" . $params . "\n" . '])';
        }

        if ($fun == 'from') {
            $final_output[] = $fn . '($sql, "@???" , [' . "\n" . $params . "\n" . '])';
        }


        $outFile = __DIR__ . "/out.txt";
        file_put_contents($outFile, implode(";\n", $final_output));


        /*
        print_r($params);

        print_r($newCommand);
        print_r($params);

        print_r($visitor);
        print_r($modifiedStmts);

        $dumper = new NodeDumper;
		echo $dumper->dump($ast) . "\n";
        */

        //$dumper = new NodeDumper;
        //echo $dumper->dump($ast) . "\n";

        die("OK!");

    }


}

$M = new mysql2pdo();

//$file = "d:\\htdocs\\clip.txt";
$file = __DIR__ . "/clip.txt";
$M->parseFile($file);

?>