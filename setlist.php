<?php
require('header.php');

$connexion=dbconnect(); 

/* Manage set list actions */
if (isset($_POST["formsongaction"])){

    $action = $_POST["formsongaction"];
    
    if ($action == "add"){

        $title = $_POST["title"];
        $artist = $_POST["artist"];
        $style = $_POST["style"];

        $sql = "INSERT INTO setlist (`title`, `artist`, `style`) VALUES(:title, :artist, :style )";
        $query = $connexion->prepare($sql);
        $query->bindValue(':title', htmlspecialchars($title), PDO::PARAM_STR);
        $query->bindValue(':artist', htmlspecialchars($artist), PDO::PARAM_STR);
        $query->bindValue(':style', htmlspecialchars($style), PDO::PARAM_STR);

        // execute insert sql
        $query->execute();

    }
    else if ($action=="modify"){

        $title = $_POST["title"];
        $artist = $_POST["artist"];
        $style = $_POST["style"];

        $id = $_POST["formsongid"];

        $sql = "UPDATE setlist SET `title` = :title, `artist`=:artist, `style`=:style WHERE id=:id";
        $query = $connexion->prepare($sql);
        $query->bindValue(':title', htmlspecialchars($title), PDO::PARAM_STR);
        $query->bindValue(':artist', htmlspecialchars($artist), PDO::PARAM_STR);
        $query->bindValue(':style', htmlspecialchars($style), PDO::PARAM_STR);
        $query->bindValue(':id', $id, PDO::PARAM_STR);

        // execute insert sql
        $query->execute();

    }
    else{

        $id = $_POST["formsongid"];

        $sql = "DELETE FROM setlist WHERE id=:id";
        //echo $sql." ".$id;
        $query = $connexion->prepare($sql);
        $query->bindValue(':id', $id, PDO::PARAM_STR);

        $query->execute();

    }

}


/* Querying Set List from DB */
$columns = array('title','artist','style');
$column = isset($_GET['column']) && in_array($_GET['column'], $columns) ? $_GET['column'] : $columns[0];
$sort_order = isset($_GET['order']) && strtolower($_GET['order']) == 'desc' ? 'DESC' : 'ASC';

$up_or_down = str_replace(array('ASC','DESC'), array('up','down'), $sort_order); 
$asc_or_desc = $sort_order == 'ASC' ? 'desc' : 'asc';
$add_class = ' class="highlight"';

$sql = "SELECT * from setlist ORDER BY " .  $column . " " . $sort_order;

if(!$connexion->query($sql)) {
  echo "Pb d'accès à la bdd"; 
}
else{ 
  ?> 

  <div class="main">
  <!-- Titre -->
    <header class="intro">
        <h1> Set List </h1>
    </header>

    <script>
        /** Search Song Filter Function */
        function searchFunction() {
            var value = document.querySelector("#searchInput").value.toLowerCase();
            document.querySelectorAll("#songTable tbody tr").forEach((tr)=>{
                tr.style.display = (tr.innerText.toLowerCase().indexOf(value)> -1)?'':'none';
            });
        }

         /** Add Or Modify JS Function (using addUpdateSongForm) */
        function addormodifySong(action, id, title, artist, style) {
            // Use hidden input (formsongaction) of addUpdateSongForm to store action (add or update) ==> it will put action in $_POST['formsongaction']
            document.querySelector("#addUpdateSongForm").elements["formsongaction"].value = action;
            
            if (action=="add"){
                // set Text to "Add"
                document.querySelector('#addUpdateSongModalLabel').innerText="Add Song";
            }
            else{
                // set Text to "Edit"
                document.querySelector('#addUpdateSongModalLabel').innerText="Edit Song";
                
                // pre-fill inputs
                document.querySelector("#addUpdateSongForm").elements["title"].value = title;
                document.querySelector("#addUpdateSongForm").elements["artist"].value = artist;
                document.querySelector("#addUpdateSongForm").elements["style"].value = style;

                // Use hidden input (formsongid) of addUpdateSongForm to store song's id ==> it will put id in $_POST['formsongid']
                document.querySelector("#addUpdateSongForm").elements["formsongid"].value = id;
            }
            
            // display modal form
            let modal = document.getElementById('addUpdateSongModal');
            modal.style.display='block';
        }

        /** JS function called before submitting add/update song form to check empty values */
        function check(){
            let valid=true;

            if (document.querySelector("#addUpdateSongForm").elements["title"].value.trim() == "") {
                valid=false;
            }
            if (document.querySelector("#addUpdateSongForm").elements["artist"].value.trim() == "") {
                valid=false;
            }
            if (document.querySelector("#addUpdateSongForm").elements["style"].value.trim() == "") {
                valid=false;
            }
            
            if (!valid){
                return false;
            }
            else{
                return true;
            }
        }

         /** Remove Song JS Function (using removeSongForm) */
         function removeSong(id) {
            // Use hidden input (formsongaction) of removeSongForm to store action (remove) ==> it will put action in $_POST['formsongaction']
            document.querySelector("#removeSongForm").elements["formsongaction"].value = "remove";
            // Use hidden input (formsongid) of removeSongForm to store song's id ==> it will put id in $_POST['formsongid']
            document.querySelector("#removeSongForm").elements["formsongid"].value = id;


            // display modal form
            let modal = document.getElementById('removeSongModal');
            modal.style.display='block';
        }

    </script>

    <!-- Add or Update Song Form DIV -->
    <div id="addUpdateSongModal" class="modal">
  
        <form id="addUpdateSongForm" onsubmit="return check();"  class="modal-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="dlgheadcontainer">
                <span onclick="document.getElementById('addUpdateSongModal').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <h1 id="addUpdateSongModalLabel">Song Edit :</h1>
            </div>

            <div class="dlgcontainer">
                <input type="hidden" name="formsongaction" id="addorupdate">
                <input type="hidden" name="formsongid" id="formsongid" >


                <label for="uname"><b>Song Title :</b></label>
                <input type="text" name="title" id="songtitle" placeholder="Song Title">

                <label for="psw"><b>Song Artist :</b></label>
                <input type="text" name="artist" id="songartist" placeholder="Song Artist">

                <label for="psw"><b>Style :</b></label>
                <input type="text" name="style" id="songstyle" placeholder="Style">
                    
                <button type="submit" class="okbtn">Apply</button>
                <button type="button" onclick="document.getElementById('addUpdateSongModal').style.display='none'" class="cancelbtn">Cancel</button>

            </div>

        </form>
    </div>

    <!-- Remove Song Form DIV -->
    <div id="removeSongModal" class="modal">
  
        <form id="removeSongForm" class="modal-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="dlgheadcontainer">
                <span onclick="document.getElementById('removeSongModal').style.display='none'" class="close" title="Close Modal">&times;</span>
                    <h1 id="removeSongModalLabel">Song Remove ?</h1>
            </div>

            <div class="dlgcontainer">
                <input type="hidden" name="formsongaction" id="addorupdate">
                <input type="hidden" name="formsongid" id="formsongid" >

                <button type="submit" class="okbtn">Yes</button>
                <button type="button" onclick="document.getElementById('removeSongModal').style.display='none'" class="cancelbtn">No</button>

            </div>

        </form>
    </div>

    <!-- Set List Table & Search filter -->
    <div class="row">
        <div class="col-sm">
            <table id="songTable" style="width:90%;margin: auto;">
                <thead>
                    <tr>
                        <th class="headersearch" colspan="5"><input type="text" class="searchinput" id="searchInput" onkeyup="searchFunction()" placeholder="Search .."></th>
                    </tr>
                    <tr>
                        <th class="headersort"><a href="./setlist.php?column=title&order=<?php echo $asc_or_desc; ?>">TITLE <i class="fas fa-sort<?php echo $column == 'title' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                        <th class="headersort"><a href="./setlist.php?column=artist&order=<?php echo $asc_or_desc; ?>">ARTIST(S) <i class="fas fa-sort<?php echo $column == 'artist' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                        <th class="headersort"><a href="./setlist.php?column=style&order=<?php echo $asc_or_desc; ?>">STYLE <i class="fas fa-sort<?php echo $column == 'style' ? '-' . $up_or_down : ''; ?>"></i></a></th>
                        <?php 
                        if ($admin){
                            ?>
                            <th colspan=2 class="headersort"><button onclick="addormodifySong('add');" type='button' class='addbtn'><i class='fa fa-plus'></i></button></th>
                            <?php
                        }
                        ?>

                    </tr>
                </thead>
                <tbody>
                <?php 
                    foreach ($connexion->query($sql) as $row) {
                        echo "<tr><td>".$row['title']."</td> <td>".
                                        $row['artist']."</td> <td>".
                                        $row['style']."</td> ";
                        if ($admin){
                            echo "<td class='align-middle'><button onclick=\"addormodifySong('modify', " . $row['id'] . ", '".addslashes($row['title'])."', '".addslashes($row['artist'])."', '".addslashes($row['style'])."');\" type='button' class='editbtn'><i class='fa fa-pen'></i></button></td>"; 
                            echo "<td class='align-middle'><button onclick='removeSong(" .  $row['id'] . ");' type='button' class='cancelbtn'><i class='fa fa-trash'></i></button></td></tr>" ;
                        }
                        else{
                            echo "</tr>";
                        }
                    }
                ?> 
                </tbody>
            </table>
        </div>
    </div>


</div>
  
<?php
}


require('footer.php');
?>