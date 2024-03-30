<?php 
require_once("../../kernel/kernel.php");

//S'il n'y a aucune session c'est que le joueur n'est pas connecté alors on le redirige vers l'accueil
if (empty($_SESSION['account'])) { exit(header("Location: ../../index.php")); }
//S'il y a actuellement un combat on redirige le joueur vers le module battle
if ($battleRow > 0) { exit(header("Location: ../../modules/battle/index.php")); }

require_once("../../html/header.php");

if (isset($_POST['token']))
{
    //Si le token de sécurité est correct
    if ($_POST['token'] == $_SESSION['token'])
    {
        //On supprime le token de l'ancien formulaire
		$_SESSION['token'] = NULL;
		
		//Comme il y a un nouveau formulaire on régénère un nouveau token
        $_SESSION['token'] = uniqid();
		
        //On fait une requête pour vérifier toutes les demandes d'échange en cours
        $tradeRequestQuery = $bdd->prepare("SELECT * FROM car_trades_requests
        WHERE tradeRequestCharacterOneId = ?");
        $tradeRequestQuery->execute([$characterId]);
        $tradeRequestRow = $tradeRequestQuery->rowCount();
        
        //S'il existe un ou plusieurs demande d'échange en attente
        if ($tradeRequestRow > 0) 
        {
            ?>
            
            <form method="POST" action="manageTradeRequest.php">
                Demande d'échange envoyée en attente : <select name="tradeRequestId" class="form-control">
        
                    <?php
                    //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                    while ($tradeRequest = $tradeRequestQuery->fetch())
                    {
                        //On récupère les valeurs de chaque objets
                        $tradeRequestId = stripslashes($tradeRequest['tradeRequestId']);
                        $tradeRequestCharacterTwoId = stripslashes($tradeRequest['tradeRequestCharacterTwoId']);
                        $tradeRequestMessage = stripslashes($tradeRequest['tradeRequestMessage']);
        
                        //On fait une requête pour récupérer le nom du personnage dans la base de donnée
                        $characterQuery = $bdd->prepare("SELECT * FROM car_characters
                        WHERE characterId = ?");
                        $characterQuery->execute([$tradeRequestCharacterTwoId]);
                        
                        //On fait une boucle sur le ou les résultats obtenu pour récupérer les informations
                        while ($character = $characterQuery->fetch())
                        {
                            //On récupère les valeurs de la demande d'échange
                            $tradeRequestCharacterName = stripslashes($character['characterName']);
                        }
                        $characterQuery->closeCursor();
                        ?>
                        <option value="<?php echo $tradeRequestId ?>"><?php echo "$tradeRequestCharacterName ($tradeRequestMessage)" ?></option>
                        <?php
                    }
                    ?>
        
                </select>
                <input type="hidden" class="btn btn-secondary btn-lg" name="token" value="<?php echo $_SESSION['token'] ?>">
                <input type="submit" name="cancelTradeRequest" class="btn btn-secondary btn-lg" value="Annuler la demande">
            </form>
        
            <?php
        }
        //S'il n'y a aucune offre de disponible on prévient le joueur
        else
        {
            ?>
            
            Il n'y a aucune demande d'échange en attente.
            
            <hr>
        
            <form method="POST" action="index.php">
                <input type="submit" class="btn btn-secondary btn-lg" name="back" value="Retour">
            </form>
            
            <?php
        }
        $tradeRequestQuery->closeCursor();
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
    echo "Erreur : Tous les champs n'ont pas été rempli";
} 

require_once("../../html/footer.php"); ?>