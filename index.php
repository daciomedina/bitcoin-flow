<?php require_once __DIR__ . '/header.php'; 
$title = "Cuánto Bitcoin puedes comprar";
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$title?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEAg3QhqLMpG8r+8fhAXLRk2vvoC2f3B09zVXn8CA5QIVfZOJ3BCsw2P0p/We" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="Resources/css/styles.css">
</head>
<body>
    <div class="main">
        <div class="container-xs">
            <h1>¿<?=$title?>?</h1>
            <div class="form">
                <div class="mb-3">
                    <label for="currency" class="form-label ">Elige tu moneda:</label>
                    <select class="form-select form-select-lg mb-3" name="currency" id="currency">
                        <?php
                        foreach($currencies as $currency){
                            echo '<option value="'.$currency.'">'.$currency.'</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="price" class="form-label ">Poder Adquisitivo: </label>
                    <input type="range" name="price" class="form-range" id="price" min="100" max="100000" step="100" value="25000">
                    <output class="price-output display-6" for="price" ></output>
                </div>


                <div style="display: none;">
                <?php
                    foreach($currencies as $currency){
                        echo '<div id="'.$currency.'">'.$bitcoin->getCurrentPrice($currency).'</div>';
                    }
                ?>
                </div>
            </div>
                <div id="Salida" class="row justify-content-center">
                    <div id="valorTotal" class="text-center display-4"></div>
                </div>
            <script>
                const price = document.querySelector('#price');
                const output = document.querySelector('.price-output');
                const currency = document.querySelector('#currency');
                const valorTotal = document.querySelector('#valorTotal');
                output.textContent = price.value;

                price.addEventListener('input', function() {
                    const currencyId = currency.value;
                    const currencyValue = document.querySelector('#'+currencyId).textContent;
                    let result=0;
                    if(currencyValue/price.value>0){
                        result = price.value/currencyValue;
                    }else{
                        result = price.value*100/currencyValue
                    }
                    output.textContent = price.value;
                    valorTotal.textContent = result.toFixed(4);
                });
            </script>
        </div>
    </div>
</body>
</html>
