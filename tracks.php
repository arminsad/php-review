<?php 

require(__DIR__ . '/vendor/autoload.php');

if (file_exists(__DIR__ . '/.env')){
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

$pdo = new PDO($_ENV['PDO_CONNECTION_STRING']);

if (!isset($_GET['playlist']) || empty($_GET['playlist'])) {
    header("Location: playlists.php");
    exit();
}
else{
$playlist_id = $_GET['playlist'];
$sql = "
SELECT tracks.name, albums.title, artists.name as artist_name, unit_price, genres.name as genre_name
FROM tracks 
INNER JOIN albums
ON tracks.album_id = albums.id
INNER JOIN artists
ON albums.artist_id = artists.id
INNER JOIN genres
ON tracks.genre_id = genres.id
INNER JOIN playlist_track
ON tracks.id = playlist_track.track_id
WHERE playlist_track.playlist_id = $playlist_id; 
";

$statement = $pdo->prepare($sql);
$statement->execute();
$tracks = $statement->fetchAll(PDO::FETCH_OBJ);
if(empty($tracks)){
    $pdo = new PDO($_ENV['PDO_CONNECTION_STRING']);
    $sql = "
    SELECT id, name 
    FROM playlists
    WHERE id = $playlist_id;
    ";

    $statement = $pdo->prepare($sql);
    $statement->execute();
    $playlist_nt = $statement->fetchAll(PDO::FETCH_OBJ);
    foreach ($playlist_nt as $playlist) :
    $playlist_name = $playlist->name;
    endforeach;
    $error = "No tracks found for " . $playlist_name;
}
}
?>
<?php if( isset($error) && !empty($error)) :?>
    <?php echo $error; ?>
<?php else : ?>
<table>
    <thead>
        <tr>
            <th>Track name</th>
            <th>Album title</th>
            <th>Artist name</th>
            <th>Price</th>
            <th>Genre name</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($tracks as $track) : ?>
        <tr>
            <td>
                <?php echo $track->name ?>
            </td>
            <td>
                <?php echo $track->title ?>
            </td>
            <td>
                <?php echo $track->artist_name ?>
            </td>
            <td>
                <?php echo $track->unit_price ?>
            </td>
            <td>
                <?php echo $track->genre_name ?>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
<?php endif; ?>