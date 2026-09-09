<?php

class ProductQuestionsSubmitModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (!Tools::isSubmit('submitQuestion')){
            return;
        }

        $question = trim((string) Tools::getValue('question'));
        $idProduct = (int) Tools::getValue('id_product');

        if($question === '' || mb_strlen($question) > 1000){
            die('Question must be between 1 and 1000 characters long.');
        }

        if ($idProduct <= 0 || !Validate::isLoadedObject(new Product($idProduct))){
            die('Invalid product ID.');
        }

        $result = Db::getInstance()->insert('product_question', [
            'id_product' => $idProduct,
            'question' => pSQL($question),
            'is_approved' => 0,
            'date_add' => date('Y-m-d H:i:s'),
        ]);

        if (!$result) {
            die('Failed to save the question');
        }

        $productUrl = $this->context->link->getProductLink($idProduct);
        Tools::redirect($productUrl . '?question_submitted=1');
    }
}