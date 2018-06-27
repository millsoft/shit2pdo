# shit2pdo

A script for converting shitty (old) sql queries to PDO, example:


Before:

```
$sql = 'UPDATE events SET mpc_provided_photographer='.(int)$_POST['mpc_provided_photographer'].', id_users__changedBy='.getParameter('userid').' WHERE id='.$GET['id'];
toDatabase($sql);
```

After:

```
$sql = "UPDATE events SET mpc_provided_photographer=:mpc_provided_photographer, id_users__changedBy=:userid WHERE id=:id";
$db->toDatabase($sql, [
'mpc_provided_photographer' => $_POST["mpc_provided_photographer"],
'userid' => getParameter(userid),
'id' => $GET["id"]
])
```

