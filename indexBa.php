<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Enregistrement des heures de travail</title>
  <link rel="stylesheet" href="/css/style.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.15.6/xlsx.full.min.js"></script>
</head>
<body>

<?php
// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=STL';
$username = 'root';
$password = 'Kaka1997@@';

try {
  $pdo = new PDO($dsn, $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  echo 'Erreur de connexion : ' . $e->getMessage();
}

// Vérification des informations d'identification lors de la soumission du formulaire
if (isset($_POST['submit'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  // Requête SQL pour récupérer les informations de l'utilisateur
  $query = "SELECT * FROM users WHERE username = :username";
  $stmt = $pdo->prepare($query);
  $stmt->execute(array(':username' => $username));
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Vérification du mot de passe
  if ($user && password_verify($password, $user['password'])) {
    // Utilisateur authentifié, afficher la section pour l'enregistrement des heures de travail
    echo '<script>';
    echo 'document.getElementById("loginContainer").style.display = "none";';
    echo 'document.getElementById("hoursEntryContainer").style.display = "block";';
    echo '</script>';
  } else {
    echo '<script>alert("Nom d\'utilisateur ou mot de passe incorrect !");</script>';
  }
}
?>
  
<div id="loginContainer">
  <h1>Connexion</h1>
  <form id="loginForm" action="" method="post">
    <label for="username">Nom d'utilisateur :</label>
    <input type="text" id="username" name="username" required><br><br>
    <label for="password">Mot de passe :</label>
    <input type="password" id="password" name="password" required><br><br>
    <button type="submit" name="submit">Se connecter</button>
  </form>
</div>

<div id="hoursEntryContainer" style="display: none;">
  <h1>Enregistrement des heures de travail</h1>

  <div id="userInfo" style="display: none;">
    <label for="firstName">Prénom :</label>
    <input type="text" id="firstName" name="firstName" required><br><br>
    <label for="lastName">Nom :</label>
    <input type="text" id="lastName" name="lastName" required><br><br>
    <label for="employeeId">Numéro matricule :</label>
    <input type="text" id="employeeId" name="employeeId" required><br><br>
  </div>

  <form id="hoursForm">
    <label for="date">Date :</label>
    <input type="date" id="date" name="date" required><br><br>
    <label for="hours">Nombre d'heures travaillées :</label>
    <input type="number" id="hours" name="hours" min="1" max="24" step="0.5" required><br><br>
    <button type="button" onclick="addRow()">Ajouter</button>
  </form>

  <table id="hoursTable" border="1" style="display: none;">
    <thead>
      <tr>
        <th>Date</th>
        <th>Nombre d'heures travaillées</th>
        <th>Prénom</th>
        <th>Nom</th>
        <th>Numéro matricule</th>
      </tr>
    </thead>
    <tbody>
    </tbody>
  </table>

  <button id="exportButton" onclick="exportToExcel()" style="display: none;">Exporter vers Excel</button>
</div>

<script>
  let data = [];

  function authenticate(event) {
    event.preventDefault();
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    // Exemple de vérification de l'authentification (remplacez-le par votre propre logique d'authentification)
    if (username === "utilisateur" && password === "motdepasse") {
      document.getElementById('loginContainer').style.display = 'none';
      document.getElementById('hoursEntryContainer').style.display = 'block';
      document.getElementById('userInfo').style.display = 'block';
      if (username === "admin") {
        document.getElementById('hoursTable').style.display = 'block';
        document.getElementById('exportButton').style.display = 'block';
      }
    } else {
      alert("Nom d'utilisateur ou mot de passe incorrect !");
    }
  }

  function addRow() {
    const date = document.getElementById('date').value;
    const hours = document.getElementById('hours').value;
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const employeeId = document.getElementById('employeeId').value;

    data.push({ date, hours, firstName, lastName, employeeId });

    const tableBody = document.querySelector('#hoursTable tbody');
    const newRow = tableBody.insertRow();
    const dateCell = newRow.insertCell(0);
    const hoursCell = newRow.insertCell(1);
    const firstNameCell = newRow.insertCell(2);
    const lastNameCell = newRow.insertCell(3);
    const employeeIdCell = newRow.insertCell(4);
    dateCell.textContent = date;
    hoursCell.textContent = hours;
    firstNameCell.textContent = firstName;
    lastNameCell.textContent = lastName;
    employeeIdCell.textContent = employeeId;
  }

  function exportToExcel() {
    const worksheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, 'Heures de travail');
    XLSX.writeFile(workbook, 'heures_de_travail.xlsx');
  }
</script>

</body>
</html>
