<?php 
require_once("custom/php/common.php");

function showTable()
{
	global $wpdb;
	$compCount = $wpdb->get_var("SELECT count(*) FROM property WHERE value_type='enum'");
	if($compCount == 0)
	{
		?>
		<div>Não há propriedades especificadas cujo tipo de valor seja enum. Especificar primeiro nova(s) propriedade(s) e depois voltar a esta opção.<br>
		</div>
		<?php 
	}
	else
	{
		$compNames = $wpdb->get_results("SELECT DISTINCT component.id, component.name FROM component, property WHERE property.value_type =  'enum' and component.state = 'active' AND property.component_id = component.id order by name");
		
		?>
		<table class = "tables" >
		  	<tr>
				<th>Componente</th>
				<th>Id</th>
				<th>Propriedade</th>
				<th>Id</th>
				<th>Valores permitidos</th>
				<th>Estado</th>
				<th>Ação</th>
		  	</tr>
	  		<?php 

			
			foreach ($compNames as $comp)
			{
				$compProp = $wpdb->get_results("select id, name from property where value_type='enum' and property.state = 'active' AND component_id = '$comp->id' order by name");
				$compCount = 0;
				foreach ($compProp as $value) 
				{
					$propValue = $wpdb->get_results("SELECT id, value, state FROM  prop_allowed_value WHERE state ='active' and property_id = '$value->id'");
					if($wpdb->num_rows == 0)
					{
						$compCount++;
					}
					else
					{
						$compCount = $compCount + $wpdb->num_rows;
					}

				}

				?>
				<tr>
				<?php
				?>
				<td colspan="1" rowspan = "<?php echo $compCount; ?>"> <?php echo $comp->name; ?> </td>

				<?php

				foreach ($compProp as $prop) 
				{
					$propValue = $wpdb->get_results("SELECT id, value, state FROM  prop_allowed_value WHERE state ='active' and property_id = '$prop->id'");
					?>
						<td colspan="1" rowspan = "<?php echo $wpdb->num_rows; ?>"> <?php echo $prop->id; ?> </td>
						<td colspan="1" rowspan = "<?php echo $wpdb->num_rows; ?>"> <a href=' <?php echo "gestao-de-valores-permitidos?estado=introducao&propriedade=" . $prop->id; ?> '> [<?php echo $prop->name; ?>] </a> </td>

					<?php
					if($wpdb->num_rows == 0)
					{
						?>
						<td colspan="4" rowspan = "1"> Não há valores permitidos definidos </td>
						</tr>
						<?php
					}
					else
					{	
						$pValueFirst = true;
						foreach ($propValue as $pValue) 
						{
							if(!$pValueFirst)
							{
								?>
								<tr>
								<?php
								$pValueFirst = false;
							}
							?>
							<td> <?php echo $pValue->id; ?> </td>
							<td> <?php echo $pValue->value; ?> </td>
							<td> <?php echo $pValue->state; ?> </td>
							<?php 
							if($pValue->state=='active')
							{ 
								?> 
								<td> [editar] [desativar] </td>
								<?php
							}
							else
							{
								?>
								<td> [editar] [ativar] </td>
								<?php
		                    }
		                    ?>
		                	</tr>
		                	<?php
						}
					}
				}
			}
			?>
		</table>
		<?php 
	}
}

function introducao()
{
	$_SESSION['property_id'] = $_REQUEST['propriedade'];

	?>
    <h3>
    	<i>Gestão de valores permitidos - Introdução</i>
    </h3>
    <form name="insertValue" id="insertValue" action="gestao-de-valores-permitidos" method="post" class="validate" >
    	<div>
		    <label for="value" class='labelDefault'>Valor: </label>
		    <input class="textboxDefault required word" type="text" name="value" /><br />
		</div>
	    <input type="hidden" name="state" value="inserir" /><br />
	    <input type="submit" class="forms action" value='Inserir valor permitido'/>
    </form>
    
    
    <?php
}


function inserir()
{
	$value = $_POST['value'];
	$property_id = $_SESSION['property_id'];
	global $wpdb;

	if(empty($value))
	{
		echo "Dados incorrectos";
		?>
		<br>
		<script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
  		</script>
  		<?php
	}
	else
	{
		?>
		<div>
			<h3><i>Gestão de valores permitidos - Inserção</i></h3>
		</div>
		
		<?php 
			
		$result = $wpdb->query("INSERT INTO prop_allowed_value (id, property_id, value, state) VALUES (NULL, '$property_id', '$value', 'active')");

		if($result)
		{
			?>
				<div>
					<i>Inseriu os dados do novo valor permitido com sucesso.</i><br>
					<i>Clique em <a href="gestao-de-valores-permitidos"><b>Continuar</b></a> para avançar.</i>
				</div>
			<?php 
		}
		else 
		{
			insertError();
		}	
	}
	
}

if(is_user_logged_in())
{
	if(current_user_can("manage_allowed_values"))
	{
		if(!$_REQUEST['estado']){
			$switch = $_POST['state'];
		}
		else{
			$switch = $_REQUEST['estado'];
		}
		switch($switch)
		{
			case "inserir":
				inserir();
				break;
			case "introducao":
				introducao();
				break;
			default:
				showTable();		
				break;	
		}
	}
	else
	{
		?>
		<div> Não tem permissões para ver esta página. </div>
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