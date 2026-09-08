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

        if ($question === '')
        {
            die('Question cannot be empty.');
        }

        if ($idProduct <= 0)
        {
            die('Invalid product ID');
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

        die('Question sent successfuly. ID: ' . (int) Db::getInstance()->Insert_ID());

    }
}