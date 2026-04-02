<?php
require_once __DIR__ . '/includes/config.php';

echo "<h2 style='color:green'>✅ Conexiunea la PostgreSQL a fost realizată cu succes!</h2>";

// Afișează tabelele existente
$stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public';");
echo "<ul>";
while ($row = $stmt->fetch()) {
    echo "<li>" . $row['table_name'] . "</li>";
}
echo "</ul>";

// Link către pagina locatii.php
echo '<a href="locatii.php" style="font-size:18px; text-decoration:none; color:blue;">➡️ Vezi locațiile disponibile</a>';
?>
