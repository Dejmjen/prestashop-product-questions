<?php

class ProductQuestionsSubmitModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (Tools::isSubmit('submitQuestion')){
            $question = Tools::getValue('question');

            die('Otrzymano pytanie: ' . htmlspecialchars($question, ENT_QUOTES, 'UTF-8'));
        }
    }
}