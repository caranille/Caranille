<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//Si le joueur n'a pas les droits administrateurs (Accès 2) on le redirige vers l'accueil
if ($accountAccess < 2) { exit(header("Location: ../../index.php")); }

require_once("../html/header.php");
?>

<p>Offrir de l'expérience</p>

<form method="POST" action="offerExperience.php">
    Expérience à offrir : <input type="number" class="form-control" name="adminOfferExperience" placeholder="Experience" required>
    Liste des personnages <select class="form-control" name="adminCharacterId" >
        <option value="0">Tous les joueurs</option>
        
        <?php
        //On fait une recherche dans la base de donnée tous les personnages
        $characterQuery = $bdd->query("SELECT * FROM car_characters
        ORDER by characterName");
        
        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
        while ($character = $characterQuery->fetch())
        {
            $adminCharacterId = stripslashes($character['characterId']);
            $adminCharacterName =  stripslashes($character['characterName']);
            ?>
            <option value="<?php echo $adminCharacterId ?>"><?php echo "$adminCharacterName"; ?></option>
            <?php
        }
        $characterQuery->closeCursor();
        ?>
    
    </select>
    <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
    <input type="submit" class="btn btn-secondary btn-lg" name="offerExperience" value="Offrir de l'expérience">
</form>

<?php require_once("../html/footer.php");