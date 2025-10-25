<?php
//connessione al db
try{
    $hostname = 'localhost';
    $dbname = 'corso';
    $user = 'root';
    $pass = '';

    $db = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    echo 'Errore: '.$e->getMessage();
    die();
}

//impostare la action di default
$action = '';

// leggere la nuova action (se viene passata)
if(isset($_GET['action'])){
    $action = $_GET['action'];
}

//imposto id
$id = 0 ;

//guardo come arriva l'id
if(isset($_REQUEST['id'])){
    $id = filter_var($_REQUEST['id'], FILTER_SANITIZE_NUMBER_INT);
}

switch($action){
    case 'lista':
        $strhtml = visualizzaElenco();
        break;
    case 'dettaglio':
        $strhtml = visualizzaDettagli();
        break;
    case 'form':
        $strhtml = visualizzaForm();
        break;
    case 'salva':
        $strhtml = salva();
        break;
    case 'elimina':
        $strhtml = elimina();
        break;
    default:
        $strhtml = visualizzaElenco();
        break;
}
?>

<!doctype html>
<html>
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta lang="it">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <title>Utenti del sito</title>
    
  </head>
  <body>
  <div class="container">
        <div class="row">
            <div class="col-md-12">
                <?php echo $strhtml; ?>                
            </div>
        </div>
    </div>
    <script>
        function elimina(id){
            var scelta = window.confirm('Sei sicuro di voler eliminare il record?');
            if(scelta){
                location.href='?action=elimina&id='+id;
            }
        }
    </script>
    <style>
 @font-face {
  font-family: Aurella;
  src: url(Aurella.ttf);
 }

    body{
        background-color:#eae6ca;
        color:black;
        font-family: Tahoma, sans-serif;
    }
    h1 {
        padding-top: 10px;
        font-family: 'Aurella'; 
        font-size: 80px;
        
    }
    h5 {
        font-size: 50px;
    }
    thead {
      border-bottom: 1px solid dodgerblue;
    }
    th, td {
    width:210px;
    
    }
    tbody tr:nth-child(odd){
        background-color:lightyellow;
    }
    .dettagli {
        background-color:#c3e4e8;
        color:darkblue;
        
    }
    .modifica {
        background-color:#b7df89;
        color: darkgreen;
    }
    .elimina {
        background-color: #ffcccb;
        color:darkred;
    }
    .btn {
        border-radius:8%;
    }
    </style>
  </body>
</html>

<?php

function visualizzaElenco(){
    global $db; 
    $search = '%';

    if(isset($_GET['search'])){
        $search = '%'. $_GET['search'] .'%';
    }

    $strhtml = '<h1>Visualizza l&nbsp;&apos;elenco degli utenti</h1>';
    $strhtml .=     '<form action="?action=lista" method="GET">';
    $strhtml .= '<div class="col-md-9 offset-md-9">';
    $strhtml .= '<input type="search" name="search" placeholder="cerca" value="'.str_replace('%','',$search).'">&nbsp;';
    $strhtml .= '<input type="submit" class="btn modifica">';
    $strhtml .= '</div>';
    $strhtml .= '<p></p>';
    $strhtml .= '<div class="col-md-10 offset-md-10">
        <a href="?action=form" class="btn modifica">&nbsp;Aggiungi utente&nbsp;</a>
        </div>';
    $strhtml .= '<table>'; 
    $strhtml .= '<tr>';
    $strhtml .=   '<td colspan="12">';
    $strhtml .=     '</form>';
    $strhtml .=   '</td>';
    $strhtml .= '</tr>';
    $strhtml .= '<thead>';
    $strhtml .=   '<tr>';
    $strhtml .=     '<th>&nbsp;Id</th>';
    $strhtml .=     '<th>&nbsp;Nome</th>';
    $strhtml .=     '<th>&nbsp;Cognome</th>';
    $strhtml .=     '<th></th>';
    $strhtml .=     '<th></th>';
    $strhtml .=     '<th></th>';
    $strhtml .=   '</tr>';
    $strhtml .= '</thead>';
    $strhtml .= '<tbody>';
    $sql='SELECT id, nome, cognome FROM utenti WHERE nome like :search';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $stmt->execute();

    if($stmt->rowCount() == 0) {
        $strhtml .= '<tr>';
        $strhtml .= '<td colspan="6">Nessun record disponibile con i criteri usati</td>';
        $strhtml .= '</tr>';
    } elseif($stmt->rowCount() == 1){
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        header('location: ?action=dettaglio&id='.$row['id']);
    } else{
        while( $row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $strhtml .= '<tr>';
            $strhtml .= '<td>&nbsp;'.$row['id'].'</td>';
            $strhtml .= '<td>&nbsp;'.$row['nome'].'</td>';
            $strhtml .= '<td>&nbsp;'.$row['cognome'].'</td>';
            $strhtml .= '<td><a class="btn dettagli" href="?action=dettaglio&id='.$row['id'].'">Dettagli</a></td>';
            $strhtml .= '<td><a class="btn modifica" href="?action=form&id='.$row['id'].'">Modifica</a></td>';
            $strhtml .= '<td><a class="btn elimina" href="javascript:elimina('.$row['id'].')">Elimina</a></td>';
            $strhtml .= '</tr>';
        }
    }
    $strhtml .= '</tbody>';
    $strhtml .= '</table>';
    return($strhtml);
}

function visualizzaDataIt($param){
    
    $anno = substr($param, 0, 4);
    $mese = substr($param, 5, 2);
    $giorno = substr($param, 8, 2);
    $param = $giorno.'-'.$mese.'-'.$anno;
    return $param;
}


function visualizzaDettagli(){
    global $db, $id;
    $sql='SELECT * FROM utenti WHERE id = :id';
    $stmt=$db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $strhtml = '<div class="prodotto">';
    $strhtml .= '<h1 class="display_6">Visualizza i dettagli dell&nbsp;&apos;utente</h1>';
    $strhtml .= '<h5>'.$row['nome'].'&nbsp;'.$row['cognome'].'</h5>';
    $strhtml .= '<p><strong>Data di nascita</strong>: '.visualizzaDataIt($row['nascita']).'</p>';
    $strhtml .= '<p><strong>Sesso</strong>: '.$row['sesso'].'</p>';
    $strhtml .= '<p><strong>Email</strong>: '.$row['email'].'</p>';
    $strhtml .= '<p><strong>Data inserimento</strong>: '.visualizzaDataIt($row['data']).'</p>';
    $strhtml .= '</div>';
    return($strhtml);
}

function visualizzaForm(){

    global $id, $db;
    $sql = 'SELECT * FROM utenti WHERE id = :id';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
      
    if($stmt->rowCount() == 0){
        $row['nome'] = '';
        $row['cognome'] = '';
        $row['sesso'] = '';
        $row['nascita'] = '';
        $row['email'] = '';
        $row['passw'] = '';
    } else{
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $strhtml = '<h1 class="display_6">Modifica o aggiungi un utente</h1>';
    $strhtml .= '<form action="?action=salva" method="POST">';
    $strhtml .= '<input type="hidden" name="id" value="'.$id.'">';
    $strhtml .= '<div>';
    $strhtml .= '<label>Nome</label><br>';
    $strhtml .= '<input type="text" name="nome" value="'.$row['nome'].'" class="form-control">';
    $strhtml .= '</div>';
    $strhtml .= '<div>';
    $strhtml .= '<label>Cognome</label><br>';
    $strhtml .= '<input type="text" name="cognome" value="'.$row['cognome'].'" class="form-control">';
    $strhtml .= '</div>';
    $strhtml .= '<div>';
    $strhtml .= '<label>Sesso</label><br>';
    $strhtml .= '<select name="sesso" class="form-control">';
    
    if($row['sesso'] == 'F'){
        $strhtml .=  '<option value="F" selected>F</option>
                      <option value="M">M</option>';
    } else {
        $strhtml .=  '<option value="M" selected>M</option>
                      <option value="F">F</option>';
    }
       
    $strhtml .= '</select>';
    $strhtml .= '</div>';
    $strhtml .= '<div>';
    $strhtml .= '<label>Data di nascita</label><br>';
    $strhtml .= '<input type="date" name="nascita" class="form-control">'.$row['nascita'].'</input>';
    $strhtml .= '</div>';
    $strhtml .= '<div>';
    $strhtml .= '<label>Email</label><br>';
    $strhtml .= '<input type="email" name="email" class="form-control">'.$row['email'].'</input>';
    $strhtml .= '</div>';
    $strhtml .= '<div>';
    $strhtml .= '<label>Password</label><br>';
    $strhtml .= '<input type="password" name="passw" class="form-control">'.$row['passw'].'</input>';
    $strhtml .= '</div>';
    $strhtml .= '<br>';
    $strhtml .= '<div>';
    $strhtml .= '<button type="submit" class="btn btn-success">Invia</button>';
    $strhtml .= '</div>';
    $strhtml .= '</form>';
    return($strhtml);
}


function salva(){
    global $db, $id;
    /*lettura */
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $cognome = $_POST['cognome'];
    $sesso = $_POST['sesso'];
    $nascita = $_POST['nascita'];
    $email = $_POST['email'];
    $passw = $_POST['passw'];
    /*validazione */
    $errore = 0;
    if(strlen(trim($nome)) < 2){ $errore = 1; }
    if(strlen(trim($cognome)) < 2){ $errore = 1; }
    if(strlen(trim($sesso)) > 1){ $errore = 1; }
    $pattern = "/^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$/";
    if(!preg_match($pattern, $email)){$errore = 1;}
    if(strlen(trim($passw)) < 8){ $errore = 1; }

    if($errore == 0){
        if($id == 0){
            $passw = password_hash($passw, PASSWORD_BCRYPT);

            $sql = 'INSERT INTO utenti(nome, cognome, sesso, nascita, email, passw) VALUES(:nome, :cognome, :sesso, :nascita, :email, :passw)';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
            $stmt->bindParam(':sesso', $sesso, PDO::PARAM_STR);
            $stmt->bindParam(':nascita', $nascita, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':passw', $passw, PDO::PARAM_STR);
            $stmt->execute();
            $strhtml = 'Record inserito <a href="?action=lista">Torna alla lista</a>';
        } else{
            //update
            $sql = 'UPDATE utenti SET nome = :nome, cognome = :cognome, sesso = :sesso, nascita = :nascita, email = :email  /*,pass = :passw*/ WHERE id = :id LIMIT 1';
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
            $stmt->bindParam(':cognome', $cognome, PDO::PARAM_STR);
            $stmt->bindParam(':sesso', $sesso, PDO::PARAM_STR);
            $stmt->bindParam(':nascita', $nascita, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':passw', $passw, PDO::PARAM_STR);
            $stmt->execute();
            $strhtml = 'Record modificato <a href="?acion=lista">Torna alla lista</a>';
        } 
    } else {
        $strhtml = 'Errore nel form <a href="#" onClick="history.go(-1)">Torna al form</a>';
        }
 return($strhtml);
}


function elimina(){
    global $db, $id;
    $sql = 'DELETE FROM utenti WHERE id = :id LIMIT 1';
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header('location: utentiNew.php');
}

?>