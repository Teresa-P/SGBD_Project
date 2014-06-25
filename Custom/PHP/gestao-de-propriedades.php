<?php 
require_once("custom/php/common.php");

function showTable()
{
	
	global $wpdb;
	$compCount = $wpdb->get_var("select count(*) from property");
	if($compCount == 0)
	{
		?>
		<div>
			Não há propriedades especificadas.<br>
		</div>
		<?php 
	}
	else
	{
		$compNames = $wpdb->get_results("select id, name from component order by name");
		?>
		<table class = "tables">
			<tr>
				<thead>
					<th>Componente</th>
					<th>Id</th>
					<th>Propriedade</th>
					<th>Tipo de valor</th>
					<th>Nome do campo no formulário</th>
					<th>Tipo do campo no formulário</th>
					<th>Tipo de unidade</th>
					<th>Ordem do campo no formulário</th>
					<th>Obrigatório</th>
				</thead>
			</tr>
		<?php 
		foreach ($compNames as $comp)
		{
			$compElems = $wpdb->get_results("select * from property where component_id = '$comp->id' order by name");
			
			$first = true;
			foreach ($compElems as $elem)
			{
				?>
				  <tr>
				    <?php 
						if($first)
						{
					?>
					    <td colspan="1" rowspan = "<?php echo $wpdb->num_rows; ?>"><?php echo $comp->name; ?></td>

					    <?php
						$first = false;
						}
						?>
					    <td><?php echo $elem->id; ?></td>
					    <td><?php echo $elem->name; ?></td>
					    <td><?php echo $elem->value_type; ?></td>
					    <td><?php echo $elem->form_field_name; ?></td>
						<td><?php echo $elem->form_field_type; ?></td>
					    <?php $unitType = $wpdb->get_results("SELECT * FROM prop_unit_type WHERE id = '$elem->unit_type_id'");?>
					    <td><?php echo $unitType['0']->name; ?></td>
					    <td><?php echo $elem->form_field_order; ?></td>
					    <td><?php if($elem->mandatory==1){echo "Sim";}else{echo "Não";}; ?></td>
					  </tr>
					  <?php
			}
		}
		?>
		</table>
		<?php 
	}
}


function insertSection()
{
	global $wpdb;
	$selectCompsNames = $wpdb->get_results("SELECT id, name FROM component");
	$selectUnitsNames = $wpdb->get_results("SELECT id, name FROM prop_unit_type");
	$enumValues = getEnumValues("property","value_type");
	$enumFormType = getEnumValues("property","form_field_type");

	?>
    
	<h3> <i>Gestão de propriedades - introdução</i> </h3>
	<form name="register-property" id="register-property" action="gestao-de-propriedades" method="post" class="validate">
  		<div>
	  		<label class='labelDefault'>Nome da propriedade: </label>
	  		<input type="text" name="propertyName" class="textboxDefault required word"/><br>
	  	</div>
	  	<div class="validate_any">
	 		<label for='value_type' class='labelDefault' >Tipo de valor: </label>
	 		<div class='ulDefault'>
		  		<?php
			  	foreach($enumValues as $value){
					?>
					<input type="radio" name="value_type" class="required" value="<?php echo $value;?>"/> <?php echo $value;?><br>
					<?php
				}
				?>
			</div>
      	</div>
      	<div>
			<label for='componentID' class='labelDefault'>Componente: </label>
			<select name="componentID" class="selectDefault required">
				<option value="" > </option>
				<?php
					foreach($selectCompsNames as $compName)
					{
						?>
						<option value="<?php echo $compName->id;?>"> <?php echo $compName->name;?></option>
						<?php
					}
				?>
			</select>
		</div>
		<div class='validate_any'>
			<label for='form_field_type' class='labelDefault'>Tipo do campo do formulário: </label>
			<div class='ulDefault'>
				<?php
				foreach($enumFormType as $value)
				{
					?>
						<input type="radio" name="form_field_type" class="required" value="<?php echo $value;?>"/> <?php echo $value;?><br>
					<?php
				}
				?>
			</div>
		</div>
		<div>
			<label for='unitName' class='labelDefault'>Tipo de unidade: </label>
			<select name="unitName" class="selectDefault">
				<option name="unitName" value="NULL"> </option>
				<?php
					foreach($selectUnitsNames as $unitName)
					{
						?>
						<option name="unitName" value="<?php echo $unitName->id;?>"> <?php echo $unitName->name;?></option>
						<?php
					}
				?>
			</select><br />
		</div>
		<div>
			<label for='form_field_order' class='labelDefault'>Ordem do campo do formulário: </label>
	  		<input type="text" name="form_field_order" min="1" class="textboxDefault required numeric"  /><br />
		</div>
		<div class='validate_any'>
			<label for='mandatory' class='labelDefault'>Obrigatório: </label>
			<div class='ulDefault'>
				<input type="radio" name="mandatory" value="1" class="required" /> <label>Sim</label> <br />
				<input type="radio" name="mandatory" value="0" class="required" /> <label>Não</label> <br />
			</div>
		</div>
		  
		<input type="hidden" name="state" value="inserir" />
		<input type="submit" class="forms action" value="Inserir propriedade" />
	</form>

	<?php
}

function stateInsert()
{
	global $wpdb;

	$propertyName = $_POST['propertyName'];
	$value_type = $_POST['value_type'];
	$componentID = $_POST['componentID'];
	$componentName = $wpdb->get_results("SELECT name FROM component WHERE id = '$componentID'");
	//$propertyIDQuery = $wpdb->get_results("SELECT id FROM property ORDER BY id DESC LIMIT 1");
	//$propertyID = $propertyIDQuery['0']->id;
	$compNameRed = mb_substr($componentName['0']->name, 0, 3);
	$form_field_type = $_POST['form_field_type'];
	$string = preg_replace('/[^a-z0-9_ ]/i', '', $propertyName);
	$unitName = $_POST['unitName'];
	$mandatory = $_POST['mandatory'];
	$form_field_name = "" . $compNameRed . "-" . $propertyID . "-" .preg_replace('/\s/', '_', $string);
    $form_field_order = $_POST['form_field_order'];

    if(!is_numeric($form_field_order))
    {
        echo "Ordem do campo do formulário tem de ser numérico";
        ?>
        <p>
            <script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
            </script>
        </p>
        <?php
    }
    else if(empty($propertyName) || empty($value_type) || !isset($componentID) || empty($form_field_type) || empty($mandatory))
    {
    	echo "Campos preenchidos incorretamente.";
        ?>
        <p>
            <script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
            </script>
        </p>
        <?php
    }
    else
    {
		?>

	    <h3>
	    	<i>Gestão de propriedades - Inserção</i>
	    </h3>

	    <?php

	    //iniciar a transaccao
		mysql_query('begin');

		if($unitName != "NULL")
		{
			$result = $wpdb->query("INSERT INTO property (id, name, component_id, value_type, form_field_name, form_field_type, unit_type_id, form_field_order, mandatory, state) VALUES (NULL, '$propertyName', '$componentID', '$value_type', '$form_field_name', '$form_field_type', '$unitName', '$form_field_order', '$mandatory', 'active');");
		}
		else
		{
			$result = $wpdb->query("INSERT INTO property (id, name, component_id, value_type, form_field_name, form_field_type, unit_type_id, form_field_order, mandatory, state) VALUES (NULL, '$propertyName', '$componentID', '$value_type', '$form_field_name', '$form_field_type', NULL, '$form_field_order', '$mandatory', 'active');");
		}

		$propertyID = $wpdb->insert_id;
		$form_field_name = "" . $compNameRed . "-" . $propertyID . "-" .preg_replace('/\s/', '_', $string);
		$wpdb->query("UPDATE  property SET  form_field_name =  '$form_field_name' WHERE  property.id =$propertyID;");
		
		if (mysql_error()) {
			mysql_query('rollback');
			echo "Erro:" . insertError();
		}
		else
		{
			mysql_query('commit');
			?>
				<div>
					<i>Inseriu os dados da nova propriedade com sucesso.</i><br>
					<i>Clique em <a href="gestao-de-propriedades"><b>Continuar</b></a> para avançar.</i>
				</div>
			<?php 
		}
	}
}

if(is_user_logged_in())
{
	if(current_user_can("manage_properties"))
	{
		switch($_POST['state'])
		{
			case "inserir":
				stateInsert();
				break;
			default:
				showTable();
				insertSection();		
				break;
		}
	}
	else
	{
		?>
		<div> Não tem permissão para ver esta página. </div>
		<?php 		
		
	}
}
else
{
	?>
	<div>Tem de fazer login para ver esta página!</div>
	<?php 
}

?>
