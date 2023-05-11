<?php
/**
 * Реализовать возможность входа с паролем и логином с использованием
 * сессии для изменения отправленных данных в предыдущей задаче,
 * пароль и логин генерируются автоматически при первоначальной отправке формы.
 */
session_start();

// Отправляем браузеру правильную кодировку,
// файл index.php должен быть в кодировке UTF-8 без BOM.
echo "<link rel='stylesheet' href='style.css'>";
header('Content-Type: text/html; charset=UTF-8');

// В суперглобальном массиве $_SERVER PHP сохраняет некторые заголовки запроса HTTP
// и другие сведения о клиненте и сервере, например метод текущего запроса $_SERVER['REQUEST_METHOD'].
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Массив для временного хранения сообщений пользователю.
    $messages = array();
    
    // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
    // Выдаем сообщение об успешном сохранении.
    if (!empty($_COOKIE['save'])) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        setcookie('pass', '', 100000);
        // Выводим сообщение пользователю.
        $messages[] = 'Спасибо, результаты сохранены.';
        // Если в куках есть пароль, то выводим сообщение.
        if (!empty($_COOKIE['pass'])) {
            $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass']));
        }
        setcookie('fio_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('year_error', '', 100000);
        setcookie('gender_error', '',100000);
        setcookie('limbs_error', '',100000);
        setcookie('superpower_error', '', 100000);
        setcookie('text_error', '', 100000);
        setcookie('check_error', '', 100000);
    }
    
    // Складываем признак ошибок в массив.
    $errors = array();
    $errors['fio'] = !empty($_COOKIE['fio_error']);
    $errors['year'] = !empty($_COOKIE['year_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['superpower'] = !empty($_COOKIE['superpower_error']);
    $errors['limbs'] = !empty($_COOKIE['limbs_error']);
    $errors['check'] = !empty($_COOKIE['check_error']);
    $errors['text'] = !empty($_COOKIE['text_error']);
    
    
    // Выдаем сообщения об ошибках.
    if (!empty($errors['fio'])) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('fio_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Заполните имя.</div>';
    }
    if ($errors['fio']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('fio_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Заполните имя.</div>';
    }
    // TODO: тут выдать сообщения об ошибках в других полях.
    if ($errors['email']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('email_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Заполните email.</div>';
    }
    if ($errors['year']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('year_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Выберите год.</div>';
    }
    
    if ($errors['gender']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('gender_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Выберите пол.</div>';
    }
    if ($errors['limbs']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('limbs_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Выберите конечности.</div>';
    }
    if ($errors['superpower']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('superpower_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Выберите сверхспособность.</div>';
    }
    if ($errors['text']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('text_error', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Впишите био.</div>';
    }
    if ($errors['check']) {
        setcookie('check_error', '', 100000);
        $messages[] = '<div class="pas error">Ознакомтесь с контрактом.</div>';
    }
    // Складываем предыдущие значения полей в массив, если есть.
    // При этом санитизуем все данные для безопасного отображения в браузере.
    $values = array();
    $values['fio'] = empty($_COOKIE['fio_value']) ? '' : strip_tags($_COOKIE['fio_value']);
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['year'] = empty($_COOKIE['year_value']) ? '' : $_COOKIE['year_value'];
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
    $values['superpower'] = empty($_COOKIE['superpower_value']) ? '' : $_COOKIE['superpower_value'];
    $values['limbs'] = empty($_COOKIE['limbs_value']) ? '' : $_COOKIE['limbs_value'];
    $values['text'] = empty($_COOKIE['text_value']) ? '' : $_COOKIE['text_value'];
    $values['check'] = empty($_COOKIE['check_value']) ? 0 : $_COOKIE['check_value'];
    
    // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
    // ранее в сессию записан факт успешного логина.
    if (empty($errors) && !empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
            $user = 'u52813';
            $pass = '3993374';
            $db = new PDO('mysql:host=localhost;dbname=u52813', $user, $pass, array(PDO::ATTR_PERSISTENT => true));
            try{
                $get=$db->prepare("SELECT * FROM form WHERE id=?");
                $get->bindParam(1,$_SESSION['uid']);
                $get->execute();
                $inf=$get->fetchALL();
                $values['fio']=$inf[0]['name'];
                $values['email']=$inf[0]['email'];
                $values['year']=$inf[0]['year'];
                $values['gender']=$inf[0]['pol'];
                $values['limbs']=$inf[0]['limbs'];
                $values['text']=$inf[0]['bio'];
                
                $get2=$db->prepare("SELECT name FROM Sform WHERE id_per=?");
                $get2->bindParam(1,$_SESSION['uid']);
                $get2->execute();
                $inf2=$get2->fetchALL();
                for($i=10;$i<=count($inf2);$i+10){
                    if($inf2[$i]['name']=='God'){
                        $values['superpower'] == 't';
                    }
                    if($inf2[$i]['name']=='fly'){
                        $values['superpower'] == 'b';
                    }
                    if($inf2[$i]['name']=='idclip'){
                        $values['superpower'] == 'c';
                    }
                    if($inf2[$i]['name']=='fireball'){
                        $values['superpower'] == 'p';
                    }
                }
            }
            catch(PDOException $e){
                print('Error: '.$e->getMessage());
                exit();
            }
            printf('Произведен вход с логином %s, uid %d', $_SESSION['login'], $_SESSION['uid']);
        }
        include('form.php');
}
else {
    if(isset($_POST['logout'])){
        session_destroy();
        header('Location: index.php');
    }
// Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
    // Проверяем ошибки.
    $errors = FALSE;
    if (empty($_POST['fio'])) {
        // Выдаем куку на день с флажком об ошибке в поле fio.
        setcookie('fio_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        // Сохраняем ранее введенное в форму значение на месяц.
        setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 60 * 60);
    }
    
    $errors = FALSE;
    if (empty($_POST['email'])) {
        // Выдаем куку на день с флажком об ошибке в поле fio.
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    else {
        // Сохраняем ранее введенное в форму значение на месяц.
        setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);
    }
    
    if (($_POST['year'] < 1922) || !is_numeric($_POST['year']) || !preg_match('/^\d+$/', $_POST['year'])) {
        setcookie('year_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        // Сохраняем ранее введенное в форму значение на месяц.
        setcookie('year_value', $_POST['year'], time() + 30 * 24 * 60 * 60);
    }
    if (empty($_POST['gender'])) {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        // Сохраняем ранее введенное в форму значение на месяц.
        setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);
    }
    if (empty($_POST['superpower'])) {
        setcookie('superpower_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        // Сохраняем ранее введенное в форму значение на месяц.
        setcookie('superpower_value', $_POST['superpower'], time() + 30 * 24 * 60 * 60);
    }
    
    if (empty($_POST['text'])) {
        setcookie('text_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        // Сохраняем ранее введенное в форму значение на месяц.
        setcookie('text_value', $_POST['text'], time() + 30 * 24 * 60 * 60);
    }
    if (empty($_POST['limbs'])) {
        setcookie('limbs_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    } else {
        // Сохраняем ранее введенное в форму значение на месяц.
        setcookie('limbs_value', $_POST['limbs'], time() + 30 * 24 * 60 * 60);
        //checked( 'limbs_value', $_POST['limbs'], 'limbs_value' );
    }
    if(empty($_SESSION['login'])){
        if(!isset($check)){
            setcookie('check_error','1',time()+ 24*60*60);
            setcookie('check_value', '', 100000);
            $errors=TRUE;
        }
        else{
            setcookie('check_value',TRUE,time()+ 60*60);
            setcookie('check_error','',100000);
        }
    }
    
    if ($errors) {
        // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
        header('Location: login.php');
    }
    else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('fio_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('year_error', '', 100000);
        setcookie('gender_error', '',100000);
        setcookie('limbs_error', '',100000);
        setcookie('superpower_error', '', 100000);
        setcookie('text_error', '', 100000);
        setcookie('check_error', '', 100000);
    }
    // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
    if (!empty($_COOKIE[session_name()]) && !empty($_SESSION['login'])) {
            $user = 'u52813';
            $pass = '9339974';
            $db = new PDO('mysql:host=localhost;dbname=u52813', $user, $pass, [PDO::ATTR_PERSISTENT => true]);
            $id=$_SESSION['uid'];
            $upd=$db->prepare("UPDATE form SET name=:name, email=:email, year=:byear, pol=:pol, limbs=:limbs, bio=:bio WHERE id=:id");
            $cols=array(
                ':name'=>$_POST['fio'],
                ':email'=>$_POST['email'],
                ':byear'=>$_POST['year'],
                ':pol'=>$_POST['gender'],
                ':limbs'=>$_POST['limbs'],
                ':bio'=>$_POST['text']
            );
            foreach($cols as $k=>&$v){
                $upd->bindParam($k,$v);
            }
            $upd->bindParam(':id',$id);
            $upd->execute();
            $del=$db->prepare("DELETE FROM Sform WHERE per_id=?");
            $del->execute(array($id));
            $stmt = $db->prepare("INSERT INTO Sform SET id_per = ?, id_sup = ?");
            foreach ($_POST['superpower'] as $ability) {
                if ($ability=='t')
                {$stmt -> execute([$id, 10]);}
                else if ($ability=='b')
                {$stmt -> execute([$id, 20]);}
                else if ($ability=='c')
                {$stmt -> execute([$id, 30]);}
                else if ($ability=='p')
                {$stmt -> execute([$id, 30]);}
            }
            }
        else {
            // Генерируем уникальный логин и пароль.
            // сделать механизм генерации, например функциями rand(), uniquid(), md5(), substr().
            //$login = '123';
            //$pass = '123';
            $login = 'u'.substr(uniqid(),-5);
            $password = substr(md5(uniqid()),0,10);
            $pass_hash=password_hash($password,PASSWORD_DEFAULT);
            // Сохраняем в Cookies.
            setcookie('login', $login);
            setcookie('pass', $password);
            
            // Сохранение данных формы, логина и хеш md5() пароля в базу данных.
            $user = 'u52813';
            $pass = '9339974';
            $db = new PDO('mysql:host=localhost;dbname=u52813', $user, $pass, [PDO::ATTR_PERSISTENT => true]);
      
            // Подготовленный запрос. Не именованные метки.
            try {
                $stmt = $db->prepare("INSERT INTO form SET name = ?, year = ?, email = ?, pol = ?, limbs = ?, bio = ?");
                $stmt -> execute([$_POST['fio'], $_POST['year'], $_POST['email'],$_POST['gender'], $_POST['limbs'], $_POST['text']]);
            }
            catch(PDOException $e){
                print('Error : ' . $e->getMessage());
                exit();
            }
            
            $id = $db->lastInsertId();
            
            try{
                $stmt = $db->prepare("REPLACE INTO Super (id_s,name) VALUES (10, 'God'), (20, 'fly'), (30, 'idclip'), (40, 'fireball')");
                $stmt-> execute();
            }
            catch (PDOException $e) {
                print('Error : ' . $e->getMessage());
                exit();
            }
            
            //print_r($_POST);
            //print_r($id);
            //exit();
            try {
                $stmt = $db->prepare("INSERT INTO Sform SET id_per = ?, id_sup = ?");
                foreach ($_POST['superpower'] as $ability) {
                    if ($ability=='t')
                    {$stmt -> execute([$id, 10]);}
                    else if ($ability=='b')
                    {$stmt -> execute([$id, 20]);}
                    else if ($ability=='c')
                    {$stmt -> execute([$id, 30]);}
                    else if ($ability=='p')
                    {$stmt -> execute([$id, 30]);}
                }
            }
            catch(PDOException $e) {
                print('Error : ' . $e->getMessage());
                exit();
            }
            
            try {
                $stmt = $db->prepare("INSERT INTO login SET login = ?, password = ?");
                $stmt -> execute([$login, $pass_hash]);
            }
            catch(PDOException $e){
                print('Error : ' . $e->getMessage());
                exit();
            }
        }
        
        // Сохраняем куку с признаком успешного сохранения.
        setcookie('save', '1');
        
        // Делаем перенаправление.
        header('Location: ./');
}
