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

        if ($idProduct <= 0 || !Validate::isLoadedObject(new Product($idProduct))){
            die('Invalid product ID.');
        }

        # Checking CSRF Token
        $submittedToken = (string) Tools::getValue('csrf_token');

        if (!hash_equals(Tools::getToken(false), $submittedToken)){
            $this->redirectToProduct($idProduct, 'invalid_token');
            return;
        }

        if($question === '' || mb_strlen($question) > 1000){
            $this->redirectToProduct($idProduct, 'invalid_question');
            return;
        }

        $result = Db::getInstance()->insert('product_question', [
            'id_product' => $idProduct,
            'question' => pSQL($question),
            'is_approved' => 0,
            'date_add' => date('Y-m-d H:i:s'),
        ]);

        if (!$result) {
            $this->redirectToProduct($idProduct, 'save_error');
            return;
        }

        $this->redirectToProduct($idProduct, 'success');
    }

    private function redirectToProduct(int $idProduct, string $status): void
    {
        $url = $this->context->link->getProductLink($idProduct);
        $separator = strpos($url, '?') === false ? '?' : '&';

        Tools::redirect($url . $separator . 'question_status=' . urlencode($status));
    }
}