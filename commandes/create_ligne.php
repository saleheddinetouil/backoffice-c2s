<?php 
// commandes/create_ligne.php 

require_once 'config/database.php'; 

$conn = dbConnect(); 

// Get all articles
$sqlArticles = "SELECT * FROM Articles"; 
$stmtArticles = $conn->query($sqlArticles); 
$articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

?>
<div class="fixed z-10 inset-0 overflow-y-auto hidden" id="modalLigneCommande" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>

    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
      <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
        <div class="sm:flex sm:items-start"> 
          <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left"> 
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
              Ajouter des lignes de commande
            </h3>
            <div class="mt-2">
              <form method="POST" action="?page=commandes&action=edit&id=<?= $_GET['id']; ?>">
                  <input type="hidden" name="commande_id" id="commandeIdInput" value="<?= $_GET['id']; ?>">
                  <div class="mb-4"> 
                      <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Rechercher:</label> 
                      <input type="text" id="search" name="search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Rechercher un article...">
                  </div>

                  <table class="table-auto w-full overflow-x-auto" id="articlesTable">
                    <thead>
                      <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">
                            <input type="checkbox" id="selectAll" class="form-checkbox h-4 w-4 text-blue-600">
                        </th> 
                        <th class="py-3 px-6 text-left">Code Article</th> 
                        <th class="py-3 px-6 text-left">Désignation</th> 
                        <th class="py-3 px-6 text-right">Prix Unitaire</th>
                        <th class="py-3 px-6 text-right">Quantité</th> 
                      </tr> 
                    </thead>
                    <tbody> 
                      <?php foreach ($articles as $article): ?> 
                          <tr class="border-b border-gray-200 hover:bg-gray-100">
                              <td class="py-3 px-6 text-left">
                                  <input type="checkbox" name="selected_articles[]" value="<?= $article['Article_Code'] ?>" class="form-checkbox h-4 w-4 text-blue-600 articleCheckbox">
                              </td>
                              <td class="py-3 px-6 text-left whitespace-nowrap"><?= $article['Article_Code'] ?></td> 
                              <td class="py-3 px-6 text-left"><?= $article['Designation'] ?></td> 
                              <td class="py-3 px-6 text-right"><?= number_format($article['Prix_Unitaire'], 2) ?></td> 
                              <td class="py-3 px-6 text-right">
                                  <input type="number" name="quantite_<?= $article['Article_Code'] ?>" min="1" value="1" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline articleQuantite"> 
                              </td>
                          </tr> 
                      <?php endforeach; ?> 
                    </tbody> 
                  </table>
    
                  <button type="submit" name="add_articles" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4"> 
                      Ajouter les articles sélectionnés 
                  </button> 
                </form> 
            </div>
          </div>
        </div>
      </div>
    
      <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="document.getElementById('modalLigneCommande').style.display = 'none'"> 
              Fermer 
          </button>
        </div> 
      </div> 
    </div>
 </div>