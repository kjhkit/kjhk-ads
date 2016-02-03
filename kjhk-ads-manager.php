<html>
    <head>
        <title>KJHK Ads Manager</title>
        <link rel="stylesheet" href="kjhk-ads-manager.css">
    </head>
    <body>

<?php
/**
 * Created by John McCain for 90.7fm KJHK
 * Date: 11/12/2015
 * Time: 9:44 AM
 */

$pass_hash = password_hash("INSERT_PASSWORD_HERE", PASSWORD_DEFAULT);

$user = $_POST['user'];
$pass = $_POST['pass'];

if($user == 'INSER_USERNAME_HERE' && password_verify($pass, $pass_hash))
{
    //ACCESS GRANTED
    echo '<div id="impressions">';
    $path = '/var/www/kjhk.org/web/kjhk-ads/';
    $results = scandir($path);

    $results = cleanFolderResults($results);

    foreach($results as $result) {
        $data_path = $path . $result . '/' . $result . '_data.json';
        if (is_file($data_path)) {

            echo '<details><summary><strong>' . $result . '</strong></summary><table border="4">';

            $data = json_decode(file_get_contents($data_path), true);
            echo    '<div class="impressions_count">
                     <tr><th>NAME</th><th>IMPRESSIONS</th></tr>';
            foreach ($data as $key => $data_entry) {
                echo '<tr><td>' . $key . '</td><td>' . $data_entry['impressions'] . '</td>';
                echo   '</tr>';
            }
            echo   '</div></table></details>';
        }
    }
    echo '</div>';
}
else {
    //ACCESS NOT YET GRANTED
    if (isset($_POST)) {
        echo'<form method = "POST" action = "kjhk-ads-manager.php">
            <div id="login_box">
            <h3 class="form_item">Username</h3><input class="form_item" type = "text" name = "user" ></input>
            </br>
            <h3 class="form_item">Password</h3><input class="form_item" type = "password" name = "pass" ></input>
            </br></br>
            <input class="form_item" id="password_submit" type = "submit" name = "submit" value = "Go" ></input>
            </div>
        </form>';
    }
}

function cleanFolderResults($results)
{
    $clean_results = array();
    foreach ($results as $result)
    {
        //Ignore undesired folders
        if ($result === '.' or $result === '..') continue;

        //add the directory to clean_results
        if (is_dir($path . $result))
        {
            $clean_results[] = $result;
        }
    }
    return $clean_results;
}

?>
    </body>
</html>
