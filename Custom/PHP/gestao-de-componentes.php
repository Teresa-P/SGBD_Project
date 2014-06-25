<?php 
require_once("custom/php/common.php");

function showTable()
{
	global $wpdb;
		$compCount = $wpdb->get_var("select count(*) from component");
	if($compCount == 0)
	{
		?>
		<div>Não há componentes.<br></div>
		<?php 
	}
	else
	{
		$compNames = $wpdb->get_results("select id, name from comp_type order by name");
	?>
		<table class = "tables">
			<tr>
			<th>Tipo</th>
			<th>Id</th>
			<th>Nome</th>
		    <th>Estado</th>
		    <th>Ação</th>
			</tr>
			<?php 
			foreach ($compNames as $comp)
			{
				$compElems = $wpdb->get_results("select id, name, state from component where comp_type_id = '$comp->id' order by name");
				
				$first = true;
				foreach ($compElems as $elem)
				{
					?>	
					<tr>
					<?php 
						if($first)
						{
							?>
							<td colspan="1" rowspan = "<?php echo $wpdb->num_rows; ?>"> <?php echo $comp->name; ?> </td>
							<?php

							$first = false;
						}

						?>
						<td> <?php echo $elem->id; ?></td>
						<td> <?php echo $elem->name; ?></td>
						<td> <?php echo $elem->state; ?></td>
	                    <?php 
							if($elem->state=='active')
							{ ?> <td> [editar] [desativar] </td><?php
						}
						else
						{
						?><td> [editar] [ativar] </td><?php
	                    }
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
	?>
	<h3>
		<i>Gestão de componentes - Introdução</i>
	</h3>
	
	<form name="register-component" id="register-component" class="validate" action="gestao-de-componentes" method="post">
		<div>
			<label for='compName' class='labelDefault'>Nome: </label>
			<input type="text" class='textboxDefault required word' name="compName"/><br>
		</div>
		<div class='validate_any'>
			<label for='compType' class='labelDefault'>Tipo: </label>
			<div class='ulDefault'>
				<?php
				$comp = $wpdb->get_results("select id, name from comp_type order by name");
				foreach($comp as $row)
				{
					?>
						<input type="radio" name="compType" value="<?php echo $row->id; ?>" class="required"/> 
						<label><?php echo $row->name ?></label> <br>
					<?php 
				}
				?>
			</div><br>
		</div>
		<div class='validate_any'>
			<label for='compState' class='labelDefault'>Estado: </label>
			<div class='ulDefault'>
				<input type="radio" name="compState" value="active" class="required"/> 
				<label> ativo</label>
				<br>
				<input type="radio" name="compState" value="inactive" class="required"/>
				<label> inativo</label>
				<br>
			</div>
			<br>
		</div>
		<div>
		    <input type="hidden" name ="state" value ="inserir"/><br>
		    <input type = "submit" class="forms action" value="Inserir componente"/><br>
		</div>
	</form>
    
    
	<?php	
}

function stateInsert()
{


	global $wpdb;
	$name = $_POST['compName'];
	$comp_type_id = $_POST['compType'];
	$state = $_POST['compState'];

	if(empty($name) || empty($comp_type_id) || empty($state))
	{
		echo "Dados Incorretos, volte a preencher.";
		?>
		<br>
		<script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
  		</script>
  		<?php
	}
	else
	{
		$result = $wpdb->query("INSERT INTO component (id, name, comp_type_id, state) VALUES (NULL, '$name', '$comp_type_id', '$state')");
		?>
		<h3>
			<i>Gestão de Componentes - Inserção</i>
	    </h3>
		<?php
		
		if($result)
		{
			?>
				<div>
					<i>Inseriu os dados do novo componente com sucesso.</i><br>
					<i>Clique em <a href="gestao-de-componentes"><b>Continuar</b></a> para avançar.</i>
				</div>
			<?php 
		}
		else
		{
			echo $name; echo "<br>";
			echo $comp_type_id; echo "<br>";
			echo $state; echo "<br>";
			insertError();
		}
	}


	
	
}


if(is_user_logged_in())
{
	if(current_user_can("manage_components"))
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
			<div>
				Não tem autorização para aceder a esta página
			</div>
		<?php 		
		
	}
}
else
{
	?>
	<div>Tem de realizar o login para vizualizar esta página!</div>
	<?php 
}

?>


