<?php
$host = 'localhost';
$username = 'lab5_user';
$password = 'password123';
$dbname = 'world';

function connectToDatabase() {
  global $host, $dbname, $username, $password;
  $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $conn;
}

function fetchData($conn, $query, $params) {
        $stmt = $conn->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}



function handleRequest() {
  if ($_SERVER['REQUEST_METHOD'] == 'GET') {
      $userInput = filter_input(INPUT_GET, 'country', FILTER_SANITIZE_STRING);
      $lookup = filter_input(INPUT_GET, 'lookup', FILTER_SANITIZE_STRING);

      $conn = connectToDatabase();
      $queryDetails = getQueryDetails($userInput, $lookup);

      if ($queryDetails !== null) {
          $results = fetchData($conn, $queryDetails['query'], $queryDetails['params']);
          displayResults($results, $queryDetails['columns']);
      } else {
          echo "<p>Invalid lookup type.</p>";
      }
  }
}

function getQueryDetails($userInput, $lookup) {
  $queryDetails = [
      'country' => [
          'query' => empty($userInput) ? 
              "SELECT name AS 'Name', continent AS 'Continent', independence_year AS 'Independence', head_of_state AS 'Head of State' FROM countries" :
              "SELECT name AS 'Name', continent AS 'Continent', independence_year AS 'Independence', head_of_state AS 'Head of State' FROM countries WHERE name LIKE :userInput",
          'params' => empty($userInput) ? [] : ['userInput' => "%$userInput%"],
          'columns' => ['Name', 'Continent', 'Independence', 'Head of State']
      ],
      'city' => [
          'query' => empty($userInput) ? 
              "SELECT cities.name AS 'Name', cities.district AS 'District', cities.population AS 'Population' FROM cities INNER JOIN countries ON countries.code = cities.country_code" :
              "SELECT cities.name AS 'Name', cities.district AS 'District', cities.population AS 'Population' FROM cities INNER JOIN countries ON countries.code = cities.country_code WHERE countries.name LIKE :userInput",
          'params' => empty($userInput) ? [] : ['userInput' => "%$userInput%"],
          'columns' => ['Name', 'District', 'Population']
      ]
  ];

  return $queryDetails[$lookup] ?? null;
}

function resultstable($results, $columns) {
  echo "<table border='1' style='width:100%; border-collapse:collapse;'>";
  echo "<thead><tr>";
  foreach ($columns as $column) {
      echo "<th style='padding:8px; text-align:left; background-color:#f2f2f2;'>$column</th>";
  }
  echo "</tr></thead>";
  echo "<tbody>";
  foreach ($results as $row) {
      echo "<tr>";
      foreach ($columns as $column) {
          echo "<td style='padding:8px;'>" . htmlspecialchars($row[$column]) . "</td>";
      }
      echo "</tr>";
  }
  echo "</tbody></table>";
}

function displayResults($results, $columns) {
  if (count($results) > 0) {
      resultstable($results, $columns);
  } else {
      echo "<p>EMPTY</p>";
  }
}

handleRequest();

?>





