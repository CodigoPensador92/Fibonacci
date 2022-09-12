<?php

/** @var yii\web\View $this */

$this->title = 'My Yii Application';

$x = !empty($_GET['from']) ? $_GET['from'] : 'нет начального числа';
$y = !empty($_GET['to']) ? $_GET['to'] : 'нет конечного числа';
?>
<div class="site-index">

    <div class="body-content">

    <form action="fibonacci" method="get">
        <label for="username"> Введите начало последовательности</label>
        <input type="number" name="from" ><br>
        <br>
        <label for="username">Введите конец последовательности</label>
        <input type="number" name="to"><br>
        <br>
        <input type="submit" value="Получить">
    </form>

    <div>
        Последовательность Фибоначи от <?= $x ?>  до <?= $y ?>
    </div>
        
    </div>
</div>

