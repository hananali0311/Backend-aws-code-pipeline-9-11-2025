<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

$region = 'eu-north-1';
$secretName = 'rds!db-2926c28b-26a1-4a84-945d-22e4fe066dcb';
$rdsEndpoint = 'rds.c3as6c0gw9zt.eu-north-1.rds.amazonaws.com';
$dbname = 'appdb';

try {
    $client = new SecretsManagerClient([
        'version' => 'latest',
        'region' => $region
    ]);

    $result = $client->getSecretValue(['SecretId' => $secretName]);
    $secret = json_decode($result['SecretString'], true);
    $username = $secret['username'];
    $password = $secret['password'];

    $conn = new mysqli($rdsEndpoint, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("❌ Database connection failed: " . $conn->connect_error);
    }

    echo "<h2>✅ Connected to RDS successfully!</h2>";
    echo "<h3 style='color:green;'>✅ Deployed successfully via CodePipeline!</h3>";


    $sql = "SELECT id, name, age, profession FROM employees";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>
                <tr><th>ID</th><th>Name</th><th>Age</th><th>Profession</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['age']}</td>
                    <td>{$row['profession']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No records found.";
    }

    $conn->close();
} catch (AwsException $e) {
    echo "❌ AWS SDK Error: " . $e->getMessage();
}
?>
