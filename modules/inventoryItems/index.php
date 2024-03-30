<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

//Si les variables $_POST suivantes existent
if (isset($_POST['token']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
		//Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = uniqid();
		
        //On fait une requête pour vérifier tous les objets qui sont dans l'inventaire du joueur
        $itemQuery = $bdd->prepare("SELECT * FROM  car_items, car_items_types, car_inventory 
        WHERE itemItemTypeId = itemTypeId
        AND itemId = inventoryItemId
        AND itemTypeName = 'Item'
        AND inventoryCharacterId = ?
		ORDER BY itemItemTypeId, itemName");
        $itemQuery->execute([$characterId]);
        $itemRow = $itemQuery->rowCount();
        
        //Si un ou plusieurs objets ont été trouvé
        if ($itemRow > 0)
        {
            ?>
            
            <form method="POST" action="viewItem.php">
                Liste des objets : <select name="itemId" class="form-control">
                        
                    <?php
                    //on récupère les valeurs de chaque joueurs qu'on va ensuite mettre dans le menu déroulant
                    while ($item = $itemQuery->fetch())
                    {
                        //On récupère les informations de l'objet
                        $itemId = stripslashes($item['itemId']); 
                        $itemName = stripslashes($item['itemName']);
                        $itemQuantity = stripslashes($item['inventoryQuantity']);
                        $itemTypeName = stripslashes($item['itemTypeName']);
                        $itemTypeNameShow = stripslashes($item['itemTypeNameShow']);
                        ?>
                        <option value="<?php echo $itemId ?>"><?php echo "[$itemTypeNameShow] - $itemName (Quantité: $itemQuantity)" ?></option>
                        <?php
                    }
                    ?>
                        
                </select>
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" name="viewItem" class="btn btn-secondary btn-lg" value="Plus d'information">
            </form>
            
            <?php
        }
        //Si aucun objet n'a été trouvé
        else
        {
            echo "Vous ne possédez aucun objets.";
        }
        $itemQuery->closeCursor();
    }
    //Si le token de sécurité n'est pas correct
    else
    {
        echo "Erreur : La session a expirée, veuillez réessayer";
    }
}
//Si toutes les variables $_POST n'existent pas
else 
{
    echo "Tous les champs n'ont pas été rempli";
}

require_once("../../html/footer.php"); ?>