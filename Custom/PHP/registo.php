<?php 
require_once("custom/php/common.php");
?>
<?php 
#	if()
?>
<?php 
$childFullName;
$birthday;
$parentFullName;
$parentPhone;
$tutorEmail;

function _date_is_valid($str)
{
	if(!empty($str)){
		$array = explode('-', $str);
	    $year = (int)$array[0];
	    $month = (int)$array[1];
	    $day = (int)$array[2];
	    $isDateValid = checkdate($month, $day, $year);
    	return $isDateValid;
	}
	else
		return false;
    
}

function stateValidar()
{
	$childFullName = $_POST['childFullName'];
	$birthday = $_POST['birthday'];
	$parentFullName = $_POST['parentFullName'];
	$parentPhone = $_POST['parentPhone'];
	$tutorEmail = $_POST['tutorEmail'];
	$check = true;
	?>
	<div class="generalDiv">
		<?php 
		if(empty($childFullName))
		{
			echo "O nome da criança é obrigatório.<br>";
			$check = false;
		}
		if(!_date_is_valid($birthday))
		{
			echo "Data de nascimento inválida.<br>";
			$check = false;	
		}
		if(empty($parentFullName))
		{
			echo "O nome do encarregado de educação é obrigatório.<br>";
			$check = false;
		}
		if(empty($parentPhone))
		{
			echo "Telefone do encarregado de educação é obrigatório.<br>";
			$check = false;
		}
		else if(strlen($parentPhone) !== 9 || !is_numeric($parentPhone))
		{
			echo "Telefone do encarregado de educação em formáto inválido<br>";
			$check = false;
		}
		if(!filter_var($tutorEmail, FILTER_VALIDATE_EMAIL) && !empty($tutorEmail))
		{
			echo "E-mail do tutor inválido.<br>";
			$check = false;
		}
		?>
	</div>
	<?php 
	if(!$check)
	{
		?>
		<script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
  		</script>
  		<?php
	}
	else 
	{
	?>
		<h3>
			<i>Dados de registo - validação</i>
		</h3>
    	<i><strong>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos?</strong></i><br />
        <br />
        Nome completo: <?php echo $childFullName; ?> <br />
        Data de nascimento: <?php echo $birthday; ?> <br />
        Encarregado de educação: <?php echo $parentFullName; ?> <br />
        Telefone do encarregado: <?php echo $parentPhone; ?> <br />
        E-mail do tutor: <?php echo $tutorEmail; ?> <br />
        <br />
        <form name='confirm' action='registo' method="post">
	        <input type = 'hidden' name='childFullName' value="<?php echo $childFullName; ?>"/>
	        <input type = 'hidden' name='birthday' value="<?php echo $birthday; ?>"/>
	        <input type = 'hidden' name='parentFullName' value="<?php echo $parentFullName; ?>"/>
	        <input type = 'hidden' name='parentPhone' value="<?php echo $parentPhone; ?>"/>
	        <input type = 'hidden' name='tutorEmail' value="<?php echo $tutorEmail; ?>"/>
	    	<button type = 'submit'  class="forms">Submeter</button>
			<input type = 'hidden' name = 'state' value ='inserir'/><br>
        </form>
        <?php  
	}	
}

function stateInserir()
{
	
	
	$childFullName = $_POST['childFullName'];
	$birthday = $_POST['birthday'];
	$parentFullName = $_POST['parentFullName'];
	$parentPhone = $_POST['parentPhone'];
	$tutorEmail = $_POST['tutorEmail'];
	?>
	<h3>
		<i>Dados de registo - inserção</i>
    </h3>
	<?php

	

	global $wpdb;
	$result = $wpdb->query("INSERT INTO child(id,name,birth_date,tutor_name,tutor_phone,tutor_email)VALUES (NULL ,  '$childFullName',  '$birthday',  '$parentFullName',  '$parentPhone',  '$tutorEmail')");
	
	if($result)
	{
		?>
		<div>
			<i>Inseriu os dados de registo com sucesso.<br></i>
			<i>Clique em <a href="index.php"><b>Continuar</b></a> para avançar.<br></i>
		</div>
		<?php 
	}
	else
	{
		insertError();
		?>
		<script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
  		</script>
  		<?php
	}
	
}

function stateDefault()
{
	?>
		<h3>
			<i>Dados de registo - introdução</i>
		</h3>
		
		<form name='register' id='register' action='registo' method='post' class='validate'>
			<div>
				<label for='childFullName' class='labelDefault'>Nome completo:</label>
				<input type = 'text' class='textboxDefault required word' name='childFullName' placeholder='Nome Completo'/><br>
			</div>
			<div>
				<label for='regBirthday' class='labelDefault'>Data de nascimento:</label>
				<input type = 'text' class='textboxDefault required' name ='birthday' id ='regBirthday' placeholder = 'AAAA-MM-DD'/><br>
				<script type="text/javascript">
					AnyTime.picker( "regBirthday",
					{
						format: "%Y-%m-%d", 
		   				labelTitle: "Data de Nascimento",
		        		labelYear: "Ano", labelMonth: "Mês", labelDay: "Dia do Mês" 
		  			} );
				</script>
			</div>
			<div>
				<label for='parentFullName' class='labelDefault'>Encarregado de educação:</label>
				<input type = 'text' class='textboxDefault required word' name = 'parentFullName' placeholder = 'Nome Completo' /><br>
			</div>
			<div>
				<label for='parentPhone' class='labelDefault'>Telefone do encarregado:</label>
				<input type = 'text' class='textboxDefault required numeric' name = 'parentPhone' placeholder = '291000000' /><br>
			</div>
			<div>
				<label for='tutorEmail' class='labelDefault'>E-mail do tutor:</label>
				<input type = 'text' class='textboxDefault' name = 'tutorEmail' placeholder = 'xxxx@xx.xx'/><br>
			</div>
            <button type = 'submit'  class="forms action"> Submeter </button>
			<input type = 'hidden' name = 'state' value ='validar'/><br>
		</form>
            
		<?php
}

if(!is_user_logged_in()) 
{
	switch ($_POST['state']) 
	{
	case "validar":
		stateValidar();
		break;
	case "inserir":
		stateInserir();
		break;
	default:
		stateDefault();
		break;
	}
}
else
{
	global $current_user;
    get_currentuserinfo();
	$loginID=$current_user->ID ;	//vai buscar o id do utilizador
	echo "Já está registado.";
}
?>