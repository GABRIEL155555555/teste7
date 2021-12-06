<?php

    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);

    $POST = file_get_contents('php://input');
    $_POST = json_decode($POST, true);

    //* 01 - Registra início da execução
    $TIME_START = microtime(true);

    //* 02 - Define timezone
    date_default_timezone_set('America/Sao_Paulo');

    //* 03 - Tenta incluir uma das Bibliotecas no arquivo
    if(include("../../../Bibliotecas/Biblioteca_01/Biblioteca.php"))
        $BIBLIOTECA = new Biblioteca_1;
    else if(include("../../../Bibliotecas/Biblioteca_02/Biblioteca.php"))
        $BIBLIOTECA = new Biblioteca_2;
    else if(include("../../../Bibliotecas/Biblioteca_03/Biblioteca.php"))
        $BIBLIOTECA = new Biblioteca_3;
    else 
        produzErroEspecial($TIME_START);

    //* 04 - Coleta dados do cliente no banco
    $DADOS_CLIENTE = $BIBLIOTECA->Conexao()->coletaDadosCliente(getallheaders()['Token']);

    if(empty($DADOS_CLIENTE)){
        echo $BIBLIOTECA->Geracao()->criaRetorno(0, 'erro', 'json', ['http' => '202', 'code' => 'XXX-YYY', 'message' => 'Usuário não encontrado na base de dados'], $TIME_START);
        exit;
    }
    //* 05 - Identifica se o cliente envia o XML
    if($DADOS_CLIENTE[0]['usa_xml'] == false){

        //* 06 - Monta array com campos obrigatórios do serviço
        $ACCEPTED = [
            [     
                'nome' => 'AgDebtd',
                'tipo' => 'Integer',
                'valorMinimo' => 1,
                'valorMaximo' => 9999,
                'opcional' => false,
            ],  
            [
                'nome' => 'grupoContaDebitada',
                'tipo' => 'Array',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 3,
                'opcional' => false,
                'campos' => [
                    [
                        'nome' => 'TpCtDebtd',
                        'tipo' => 'String',
                        'tamanhoMinimo' => 2,
                        'tamanhoMaximo' => 2,
                        'opcional' => false,
                    ],  
                    [
                        'nome' => 'CtDebtd',
                        'tipo' => 'Integer',
                        'valorMinimo' => 1,
                        'valorMaximo' => 9999999999999,
                        'opcional' => false,
                    ],
                    [
                        'nome' => 'CtPgtoDebtd',
                        'tipo' => 'Integer',
                        'valorMinimo' => 1,
                        'valorMaximo' => 99999999999999999999,
                        'opcional' => false,
                    ],   

                ],
            ],
            [
                'nome' => 'TpPessoaDebtd_Remet',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 1,
                'opcional' => false,
            ],
            [
                'nome' => 'CNPJ_CPFCliDebtd_Remet',
                'tipo' => 'String',
                'tamanhoMinimo' => 11,
                'tamanhoMaximo' => 18,
                'opcional' => false,
            ],   
            [     
                'nome' => 'NomCliDebtd_Remet',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 80,
                'opcional' => false,
            ],  
            [
                'nome' => 'ISPBIFCredtd',
                'tipo' => 'Integer',
                'valorMinimo' => 1,
                'valorMaximo' => 99999999,
                'opcional' => false,
            ],
            [     
                'nome' => 'AgCredtd',
                'tipo' => 'Integer',
                'valorMinimo' => 1,
                'valorMaximo' => 9999,
                'opcional' => false,
            ],  
            [     
                'nome' => 'CtCredtd',
                'tipo' => 'Integer',
                'valorMinimo' => 1,
                'valorMaximo' => 9999999999999,
                'opcional' => false,
            ],  

            [     
                'nome' => 'TpPessoaDestinatario',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 1,
                'opcional' => false,
            ],  
            [
                'nome' => 'CNPJ_CPFDestinatario',
                'tipo' => 'String',
                'tamanhoMinimo' => 11,
                'tamanhoMaximo' => 18,
                'opcional' => false,
            ],  

            [     
                'nome' => 'NomDestinatario',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 80,
                'opcional' => false,
            ],  
            [
                'nome' => 'NumContrtoOpCred',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 40,
                'opcional' => false,
            ],  
            [     
                'nome' => 'VlrLanc',
                'tipo' => 'Float',
                'valorMinimo' => -100000000000000000,
                'valorMaximo' => 100000000000000000,
                'opcional' => false,
            ],  

            [     
                'nome' => 'FinlddCliVarj',
                'tipo' => 'Integer',
                'valorMinimo' => 1,
                'valorMaximo' => 99999,
                'opcional' => false,
            ],  
            [
                'nome' => 'CodIdentdTransf',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 25,
                'opcional' => false,
            ],  
            [     
                'nome' => 'Hist',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 200,
                'opcional' => false,
            ],  
            [
                'nome' => 'NivelPrefPAG',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 1,
                'opcional' => false,
            ],  
            [     
                'nome' => 'DtMovto',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 19,
                'opcional' => false,
            ],  
            [     
                'nome' => 'DtAgendt',
                'tipo' => 'String',
                'tamanhoMinimo' => 1,
                'tamanhoMaximo' => 19,
                'opcional' => false,
            ],  
                

            ];

                       
        //* 07 - Valida os campos do serviço
        $camposValidos = $BIBLIOTECA->Validacao()->verificaCamposServico($ACCEPTED, $_POST, $TIME_START, $DADOS_CLIENTE[0]['id_empresa']);
        unset($ACCEPTED);

        if(!$BIBLIOTECA->Validacao()->validaData($_POST['DtMovto'])){
            echo $BIBLIOTECA->Geracao()->criaRetorno($DADOS_CLIENTE[0]['ispb'], 'erro', 'json', ['http' => '406', 'code' => 'VAL-003', 'message' => 'Data inválida'], $TIME_START);
        exit;
            
        }

        if(!$BIBLIOTECA->Validacao()->validaData($_POST['DtAgendt'])){
            echo $BIBLIOTECA->Geracao()->criaRetorno($DADOS_CLIENTE[0]['ispb'], 'erro', 'json', ['http' => '406', 'code' => 'VAL-003', 'message' => 'Data inválida'], $TIME_START);
        exit;
            
        }
        
        // TRATAMENTO DOS CAMPOS; TIPOCHAVE, TIPOCONTA E MOTIVO
        if($camposValidos){
            /*
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
             *                                                               *
             *    PROCESSO ESTRUTURADO DE FORMA PARTICULAR PARA CADA SCRIPT  *
             *                                                               *
             * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
            */

            // TRATAMENTO DOS CAMPOS TIPOCHAVE

            
            //Validar CPF CNPJ


            //* 08 - Processamento para montagem do XML 
        
           
           //Caso não tenha no request body não tenha o ISPB do creditante o ISPB DO destinatário será o do bacen.
           if(!isset($_POST['ispbIfCreditada'])){
            $IspbDestinatario = $BIBLIOTECA->Conexao()->coletaISPBBacen(getallheaders()['Token']);
        }else{
            $IspbDestinatario = $_POST['ispbIfCreditada'];
        }

        //Validar CPF CNPJ


        //* 08 - Processamento para montagem do XML 
        $dominioSistema = 'SPB01'; //Mudar de acordo com o dominio do sistema
        $NUOp = $BIBLIOTECA->Geracao()->geraNUOp($DADOS_CLIENTE[0]['cnpj'], $dominioSistema);  
        if(!$NUOp){
            echo $BIBLIOTECA->Geracao()->criaRetorno($DADOS_CLIENTE[0]['ispb'], 'erro', 'json', ['http' => '500', 'code' => 'VAL-001', 'message' => 'O código de dominio inserido não existe.'], $TIME_START);
            exit;
        }
        
        //? EXEMPLO DE MONTAGEM DE XML COM XMLWriter - https://www.php.net/manual/pt_BR/book.xmlwriter.php
        $XW = xmlwriter_open_memory();
        xmlwriter_set_indent($XW, 1);
        $RES = xmlwriter_set_indent_string($XW, ' ');
        xmlwriter_start_document($XW, '1.0');   
        xmlwriter_start_element($XW, 'DOC');  
        xmlwriter_write_attribute($XW, 'xmlns', 'https://www.bcb.gov.br/PAG/PAG0142.xsd');  //Substituir pelo arquivo correto 

        //Cabeçalho
        $BIBLIOTECA->Geracao()->geraBCMSG($XW, $DADOS_CLIENTE[0]['ispb'], $IspbDestinatario, $dominioSistema, $NUOp); 

        // Conteudo/SISMSG
        xmlwriter_start_element($XW, 'SISMSG');
        xmlwriter_start_element($XW, 'PAG0142'); //Substituir pelo serviço correto

        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'CodMsg','PAG0107');
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'NumCtrlIF','SLC9876543');
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'ISPBIFDebtd','90400288');
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'AgDebtd', $_POST['AgDebtd']);
        xmlwriter_start_element($XW, 'grupoContaDebitada');
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'TpCtDebtd', $_POST['grupoContaDebitada']['TpCtDebtd']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'CtDebtd', $_POST['grupoContaDebitada']['CtDebtd']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'CtPgtoDebtd', $_POST['grupoContaDebitada']['CtPgtoDebtd']);
        xmlwriter_end_element($XW);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'TpPessoaDebtd_Remet', $_POST['TpPessoaDebtd_Remet']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'CNPJ_CPFCliDebtd_Remet', $_POST['CNPJ_CPFCliDebtd_Remet']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'NomCliDebtd_Remet', $_POST['NomCliDebtd_Remet']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'ISPBIFCredtd', $_POST['ISPBIFCredtd']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'AgCredtd', $_POST['AgCredtd']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'CtCredtd', $_POST['CtCredtd']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'TpPessoaDestinatario', $_POST['TpPessoaDestinatario']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'CNPJ_CPFDestinatario', $_POST['CNPJ_CPFDestinatario']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'NomDestinatario', $_POST['NomDestinatario']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'NumContrtoOpCred', $_POST['NumContrtoOpCred']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'VlrLanc', $_POST['VlrLanc']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'FinlddCliVarj', $_POST['FinlddCliVarj']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'CodIdentdTransf', $_POST['CodIdentdTransf']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'Hist', $_POST['Hist']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'NivelPrefPAG', $_POST['NivelPrefPAG']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'DtMovto', $_POST['DtMovto']);
        $BIBLIOTECA->Geracao()->criaElementoSimplesXML($XW, 'DtAgendt', $_POST['DtAgendt']);
        
        xmlwriter_end_element($XW); 
        xmlwriter_end_element($XW);
        
        $BIBLIOTECA->Geracao()->geraUSERMSG($XW);
        xmlwriter_end_element($XW);


            //Obtém buffer da montagem como uma string
            $XML_PARA_ENVIO = xmlwriter_output_memory($XW);

            echo "$XML_PARA_ENVIO";
            exit;
            
            //* 09 - Validar montagem do XML(independente para cada script)
            if($XML_PARA_ENVIO == ''){
                echo $BIBLIOTECA->Geracao()->criaRetorno($DADOS_CLIENTE[0]['ispb'], 'erro', 'json', ['http' => '500', 'code' => 'REQ-000', 'message' => 'Ocorreu um erro interno no processamento da chamada'], $TIME_START);
                exit;
            }

        } else {
            //Biblioteca produz o echo na mensagem de erro automaticamente
            exit;
        }

    } else {

        $XML_PARA_ENVIO = $_POST;

    }
    
    //* 10 - Envia XML para ser processado no HSM no datacenter e enviado para o BACEN

    $respostaDatacenter = $BIBLIOTECA->HSM()->enviaParaHSM($DADOS_CLIENTE[0]['ispb'], '00000000', 'SPB', $XML_PARA_ENVIO, $TIME_START);

    //* 11 - Imprime o retorno do processamento desse XML já tratado pela Bilioteca
    echo $respostaDatacenter;
    exit;

    function produzErroEspecial($TIME_START){

        echo json_encode([
            'http_status_code' => '503',
            'http_status_message' => 'Service Unavailable',
            'error' => [
                'code' => 'REQ-000',
                'message' => 'Serviço temporáriamente indisponível'
            ],
            'date' => date('Y-m-d H:i:s'),
            'total_execution_time' => (microtime(true) - $TIME_START) * 1000
        ]);
        exit;

    }

?>