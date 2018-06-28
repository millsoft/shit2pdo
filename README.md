# shit2pdo

## WTF?

A script for converting shitty (old) mysql queries to PDO. This is just a personal project for one of our projects. Cloning is not recommended as it won't solve any of your problems. But go ahead and check the code if you like.

## Why?
One of my current tasks is to convert old php-mysql code into the modern and secure PDO. The project consists of hundreds of php files with 500-10000 lines of code each. Doing this by hand is a reason to hang myself on a tree with easy access to bears. Because I want to live a long and fulfilled life I created this helper tool.

## Requirments
- Autohotkey
- PHP 7
- Shitty PHP-MySQL code.

## How does it work?
If you press a hotkey on your keyboard, it will be copied into the clipboard. How? I use autohotkey for this task. Then the clipboard is saved as a text file. Then a php script (`go.php`) is called which precesses the clipboard date from the text file. Then a new text file is written back which then autohotkey reads and pastes into the editor by pressing ctrl+v.

For parsing I use PHP-Parser by nikic.

### Why not automate this completly?
I could write a script that converts the mysql code automatically but some queries are still very complex, with this semi automatic mode I have still the control over the output and can adjust it accordinaly. Short: I don't trust this shit. I am scared of destroying the code.. so thats why just semi automatic.

## Example

So here you go, the script converts 

this:

```
$sql = 'UPDATE events SET mpc_provided_photographer='.(int)$_POST['mpc_provided_photographer'].', id_users__changedBy='.getParameter('userid').' WHERE id='.$GET['id'];
toDatabase($sql);
```

into this:

```
$sql = "UPDATE events SET mpc_provided_photographer=:mpc_provided_photographer, id_users__changedBy=:userid WHERE id=:id";
$db->toDatabase($sql, [
'mpc_provided_photographer' => $_POST["mpc_provided_photographer"],
'userid' => getParameter('userid'),
'id' => $GET["id"]
])
```

