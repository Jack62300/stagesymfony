<?php

// déclaration des classes PHP qui seront utilisées
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;

//Gestion des dates 
ini_set('date.timezone', 'Europe/Paris');
$date = date('Y-m-d h:i:s');

// activation de la fonction autoloading de Composer
require __DIR__.'/../vendor/autoload.php';

// instanciation du chargeur de templates
$loader = new Twig_Loader_Filesystem(__DIR__.'/../templates');
// activation du mode debug et du mode de variables strictes
$twig = new Twig_Environment($loader, [
    'debug' => true,
    'strict_variables' => true,
]);

// chargement de l'extension Twig_Extension_Debug
$twig->addExtension(new Twig_Extension_Debug());

// création d'une variable avec une configuration par défaut
$config = new Configuration();

// création d'un tableau avec les paramètres de connection à la BDD
$connectionParams = [
    'driver'    => 'pdo_mysql',
    'host'      => '127.0.0.1',
    'port'      => '3306',
    'dbname'    => 'stage',
    'user'      => 'root',
    'password'  => '',
    'charset'   => 'utf8mb4',
];

// connection à la BDD
// la variable `$conn` permet de communiquer avec la BDD
$conn = DriverManager::getConnection($connectionParams, $config);

$errors = [];
$lastname ='';
$firstname = '';
$email = '';
$message = '';
$categorie = '';
if ($_POST) {

    // Vérification du lastname
    if (empty($_POST['lastname'])) {
        $errors['lastname'] = 'Veuillez renseignez le champ Lastname';
    } 
    else {
        $lastname = $_POST['lastname'];
    }
    /********************************/
    // vérification du firstname
    if(empty($_POST['firstname'])){
        $errors['firstname'] = 'veuillez renseignez le champ Firstname';
    }
    else {
        $firstname = $_POST['firstname'];
    }
    // Vérification de l'email
    if(empty($_POST['email'])){
        $errors['email'] = 'veuillez renseignez le champ email';
    }
    else {
        $email = $_POST['email'];
    }
    /********************************/
    // verification du message 
    if(empty($_POST['message'])){
        $errors['message'] = 'veuillez renseignez le champ message';
    }
    else {
        $message = $_POST['message'];
    }

    // verification de la categorie
    if(empty($_POST['categorie'])){
        $errors['categorie'] = 'veuillez renseignez le champ catégorie';
    }
    else {
        $categorie = $_POST['categorie'];
    }
    /********************************/
    if (!$errors) {
        // insert en BDD
        

    
        $count = $conn->executeUpdate('INSERT INTO send (lastname, firstname, email, message, categorie, datesend) VALUES (:lastname, :firstname, :email, :message, :categorie, :datesend)', [
            'lastname' => $lastname,
            'firstname' => $firstname,
            'email' => $email,
            'message' => $message,
            'categorie' => $categorie,
            'datesend' => $date,
        ]);
        
        // récupération de l'id de la dernière ligne créée par la BDD dans la variable `$lastInsertId`
        $lastInsertId = $conn->lastInsertId();
        
        // affichage de nombre de lignes affectées
        echo 'inserted : '.$count.'<br />';
        echo '<br />';
    }
}
var_dump($_POST);
// envoi d'une requête SQL à la BDD et récupération du résultat sous forme de tableau PHP dans la variable `$items`
$categorie = $conn->fetchAll('SELECT * FROM categorie');
$usersWithCategorie = $conn->executeQuery('SELECT * FROM user INNER JOIN categorie ON user.cat_id = categorie.id');

// parcours de chacun des éléments du tableau `$items`

// affichage du rendu d'un template
echo $twig->render('hello-doctrine-dbal.html.twig', [
    // transmission de données au template
    'categorie' => $categorie,
    'usersWithCategorie' => $usersWithCategorie,
    'lastname' => $lastname,
    'errors' => $errors,
]);
