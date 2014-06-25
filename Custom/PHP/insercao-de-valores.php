<?php
require_once("/custom/php/common.php");

function procurarCrianca()
{
	?>
	<h3> 
		<i>Inserção de valores - Criança - Procurar</i> 
	</h3>
    
	<form name="procurar" action="insercao-de-valores" method="post" class="validate">
        <div>
	        <label class="labelDefault" for ="childName">Nome: </label>
	        <input type="text" name="childName" class="textboxDefault"/>
	    </div>
	    <div>
	        <label class="labelDefault" for ="regBirthday">Data de nascimento: </label>
	        <input type = 'text' name = 'birthday' id = 'regBirthday' placeholder = 'AAAA-MM-DD' class='textboxDefault'/>	
	        <script type="text/javascript">
	            AnyTime.picker( "regBirthday",
	            {
	                format: "%Y-%m-%d", 
	                labelTitle: "Data de Início",
	                labelYear: "Ano", labelMonth: "Mês", labelDay: "Dia do Mês" 
	            } );
        	</script>
	    </div>
        
        <input type="hidden" name="state" value="escolher_crianca"/>
        <input type="submit" class="forms" value='Procurar'/>
	</form>
	<?php
}

function escolherCrianca()
{
    global $wpdb;
    $childName = $_POST['childName'];
    $birthday  = $_POST['birthday'];
    
    if ($childName && $birthday)
    {
        $resultadoProcura = $wpdb->get_results("SELECT id, name, birth_date FROM child WHERE name LIKE '%$childName%' AND birth_date = '$birthday'");
    } //$childName && $birthday
    elseif ($childName)
    {
        $resultadoProcura = $wpdb->get_results("SELECT id, name, birth_date FROM child WHERE name LIKE '%$childName%'");
    } //$childName
    elseif ($birthday)
    {
        $resultadoProcura = $wpdb->get_results("SELECT id, name, birth_date FROM child WHERE birth_date = '$birthday'");
    } //$birthday
	else 
	{
		$resultadoProcura = $wpdb->get_results("SELECT id, name, birth_date FROM child");
	}
	?>
    
	<h3> 
		<i>Inserção de valores - criança - escolher</i> 
	</h3>

	<ul>
		<?php
	    if ($wpdb->num_rows > 0)
	    {
	    	foreach ($resultadoProcura as $resultado)
	        {
			?>
			<li>
				<a href="insercao-de-valores?estado=escolher_comp&crianca=<?php echo $resultado->id; ?>">[<?php echo $resultado->name; ?>]</a> &nbsp;(<?php echo $resultado->birth_date; ?>)
			</li>

			<?php
	        } //$resultadoProcura as $resultado
		?>
	</ul>

	<script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
	</script>
	<?php

    } //$wpdb->num_rows > 0
    else
    {
		?>
		<div>Não foram obtidos resultados.</div>
        <script type='text/javascript'>document.write("<a href='javascript:history.back()' class='backLink'>Voltar</a>")
		</script>
		<?php
    }
}

function escolherComp()
{
    $_SESSION['child_id'] = $_REQUEST['crianca'];
    global $wpdb;
    $comps = $wpdb->get_results("SELECT name, id FROM comp_type");
    
	?>
    <h3> 
    	<i>Inserção de valores - Escolher componente</i> 
    </h3>

	<ul>
	<?php
	    foreach ($comps as $row)
	    {
	        $compsName = $wpdb->get_results("SELECT id, name, comp_type_id FROM component WHERE comp_type_id = '$row->id'");
			?>
			<li>
				<?php echo $row->name; ?>
		        <ul>
			        <?php
					
			        foreach ($compsName as $row)
			        {
						?>
						<li>
							<a href="insercao-de-valores?estado=introducao&comp=<?php echo $row->id; ?>">[<?php echo $row->name; ?>]</a>
						</li>
						<?php
						
			         } //$compsName as $row
					?>
				</ul>

			</li>
			<?php
		} //$comps as $row
		
		?>
	</ul>
	
	<?php
}

function introducao()
{
	global $wpdb;
	$_SESSION['comp_id'] = $_REQUEST['comp'];
	$comp_id = $_SESSION['comp_id'];
	$comp = $wpdb->get_results("SELECT * FROM component WHERE id = '$comp_id'");
	$_SESSION['comp_name'] = $comp[0]->name;
	$_SESSION['comp_type_id'] = $comp[0]->comp_type_id;
	
	$i = 0;
	$formName = comp_type_ . $_SESSION['comp_type_id'] . _comp_ . $_SESSION['comp_id'];
	
	?>
	<h3>
		<i>Inserção de valores - <?php echo $_SESSION['comp_name'];?></i>
	</h3>
	    
	<form name="<?php echo $formName;?>" id="<?php echo $formName;?>" action="insercao-de-valores?estado=validar&comp=<?php echo $comp_id;?>" method="post">
		<?php
	    $properties = $wpdb->get_results("select * from property where component_id ='$comp_id' and state = 'active'");
		$_SESSION['properties'] = $properties;
		foreach ($properties as $prop)
		{
			$fieldName = $prop->form_field_name;
			$fieldType = $prop->form_field_type;
			?>
			<br>
            
			<label for="<?php echo $fieldName ?>" class="labelDefault"><?php echo $prop->name;?></label>
				
			<?php
				
			$switchType = $prop->value_type;
				
			switch ($switchType)
			{
				case "enum":
					$prop_allowed_values = $wpdb->get_results("select * from prop_allowed_value where property_id = '$prop->id' and state = 'active'");
					?> 
					<div class="ulDefault"> 
						<?php 
						foreach ($prop_allowed_values as $value) 
						{
							$myValue=$value->value;
							
							if($prop->unit_type_id)
							{
								$unitTypeName = $wpdb->get_results("select name from prop_unit_type where id = $prop->unit_type_id");
								
							}
							
							?>
								<label for="<?php echo $fieldName ?>"><?php echo $myValue;?></label>
								<input type = '<?php echo $fieldType?>' name='<?php echo $fieldName ?>' value='<?php echo $myValue;?>'/>
								<label><?php echo $unitTypeName[0]->name;?></label><br>

							<?php

						}
						?>
					</div>
					<br>
					<?php
					break;
					
				case "bool":
					$myValue=$value->value;
							
					if($prop->unit_type_id)
					{
						$unitTypeName = $wpdb->get_results("select name from prop_unit_type where id = $prop->unit_type_id");
						
					}
					?>
					<div class="ulDefault">
						<input type="radio" name='<?php echo $fieldName ?>' value="1"/> 
						<label> Sim </label>
						<label><?php echo $unitTypeName[0]->name;?></label> <br />
						<input type="radio" name='<?php echo $fieldName ?>' value="0"/>
						<label> Não </label>
						<label><?php echo $unitTypeName[0]->name;?></label> <br />
						
					</div>  
					<br />
					<?php
					break;
				default:
					$myValue=$value->value;
					if($prop->unit_type_id)
					{
						$unitTypeName = $wpdb->get_results("select name from prop_unit_type where id = $prop->unit_type_id");
						
					}

					if($fieldType == "textbox")
					{
						?>
						<input class="textboxDefault" type = '<?php echo $fieldType?>' name='<?php echo $fieldName ?>'/>
						<label><?php echo $unitTypeName[0]->name;?></label> <br />
						<?php
					}
					else
					{
						?>
						<input type = '<?php echo $fieldType?>' name='<?php echo $fieldName ?>'/>
						<label><?php echo $unitTypeName[0]->name;?></label> <br />
						<?php
					}
					
					break;
			}//switch
			?> </ul> <?php
				
			
		}//foreach ($properties as $prop)
		?> <br> <?php
	
		?>
		<input type="hidden" value="validar" name="state"/>
		<input type="submit" class="forms" value="Submeter"/>
	</form> 
	<?php 
}
 
function validar()
{
	global $wpdb;
	$comp_id = $_SESSION['comp_id'];
	$comp_name = $_SESSION['comp_name'];
	$childID = $_SESSION['child_id'];
	$childName = $wpdb->get_VAR("SELECT name from child WHERE id = $childID");
	$propID = $_SESSION['properties'];
	
	?>
	<h3>
		<i> Inserção de valores - <?php echo $comp_name;?> - Validar</i>
	</h3>

	<?php 
	$fillField = false;
	$allEmpty=true;
	$noEmpty=false;
	foreach ($propID as $id)
	{
		$field = $id->form_field_name;
		$fieldtype= $id->form_field_type;
		$fieldPost = $_POST[$field];
		if($fieldpost==0 && $fieldtype!='checkbox')
		{
			$noEmpty=true;
		}
		if(!$fieldPost && $noEmpty==false)
		{
			
			if($id->mandatory==1)
			{
				$fillField = true;
				$noEmpty=false;
				?><div><?php echo $id->name ?></div><br>
				<?php
			}
		}
		else
		{
			$allEmpty=false;
		}
	}
	
	if($fillField)
	{
		?>
		<div>Os campos acima mencionados são de preenchimento obrigatório</div>
		<div> Por favor clique em <a href='javascript:history.back()'>voltar</a> para preencher o campo.</div>
		<?php
	}
	
	else if($allEmpty && !$fillField)
	{
		?>
		<div>Não preencheu qualquer campo</div>
		<div> Por favor clique em <a href='javascript:history.back()'>voltar</a> para preencher o(s) campo(s).</div>
		<?php
	}
	
	else
	{
		?>
		<i>
			<strong>Estamos prestes a inserir os dados abaixo na base de dados. Confirma que os dados estão correctos e pretende submeter os mesmos?</strong>
		</i><br />
        <br />
        <?php echo $childName; ?> <br />
        Componente: <?php echo $comp_name; ?> <br />
        <?php
        foreach ($propID as $id) 
		{
   			$field = $id->form_field_name;
			$fieldPost = $_POST[$field];
			
			if($fieldPost || $fieldPost=="0")
			{
				echo $id->name; ?>: <?php
				echo $fieldPost; ?> <br />
            
			<?php 
            }
		}
		?>
		<br />
		<form name='confirm' action="insercao-de-valores?estado=inserir&comp=<?php echo $comp_id;?>" method="post">
			<input type = 'hidden' name='childID' value="<?php echo $childID; ?>"/>
                
            <?php
							
			foreach ($propID as $id) 
			{
					
				$field = $id->form_field_name;
				$fieldPost = $_POST[$field];
				if(!empty($fieldPost))
				{
					?>
					<input type = 'hidden' name="<?php echo $field; ?>" value="<?php echo $fieldPost; ?>"/>
					<?php
						
				}
			}
			
            ?>
            <input type = 'submit'  class="forms" value="Submeter"/>
			<input type = 'hidden' name = 'state' value ='inserir'/><br>
		</form>
		<?php 
	} 
	$_SESSION['childID'] = $childID;
	$_SESSION['properties'] = $propID;
	
}

function inserir()
{
	global $wpdb;
	$comp_name = $_SESSION['comp_name'];
	$childID = $_SESSION['childID'];
	$propID = $_SESSION['properties'];
	$current_user = wp_get_current_user();
	$user = $current_user->user_login;
	
	$instantDate = date('Y-m-d');
	$blogdate = current_time( 'mysql' ); 

	?>
	<h3><i> Inserção de valores - <?php echo $comp_name;?> - Inserção</i></h3>
	<?php 
	
	foreach ($propID as $id) 
	{
		$idProp = $id-> id;
		$field = $id->form_field_name;
		$fieldPost = $_POST[$field];
					
		if($fieldPost || $fieldPost=="0")
		{
			$result = $wpdb->query("INSERT INTO value(id,child_id,property_id,value,date,time,producer) VALUES (NULL,'$childID','$idProp',  '$fieldPost','$instantDate','$blogdate','$user')");												
		}
		
	}
	if($result)
	{
		?>
		<div>
			<i>Inseriu os dados de registo com sucesso.<br></i>
			<i>Clique em <a href="insercao-de-valores">Voltar</a> para voltar ao início da inserção de valores ou em <a href="insercao-de-valores?estado=escolher_comp&crianca=<?php echo $childID; ?>">Escolher componente</a> se quiser continuar a inserir valores associados a esta criança <b>Voltar</b></a> ou em E<br></i>
		</div>
	<?php 
	}
	else 	
	{	
		insertError();
	}
}


if (is_user_logged_in())
{
    if (current_user_can("insert_values"))
    {
        if (!$_REQUEST['estado'])
        {
            $switch = $_POST['state'];
        }
        else
        {
            $switch = $_REQUEST['estado'];
        }
        switch ($switch)
        {
            case "escolher_crianca":
                escolherCrianca();
                break;
            case "escolher_comp":
                escolherComp();
                break;
            case "introducao":
                introducao();
                break;
            case "validar":
            	validar();
            	break;
			case "inserir":
            	inserir();
            	break;
            default:
                procurarCrianca();
                break;
        } //$switch
    } //current_user_can("insert_values")
    else
   	{
        //por isto numa função
		?>
		<div> Não tem permissões para ver esta página. </div>
		<?php
     }
} //isLoggedIn()
else
{
	?>
	<div>Tem de fazer login para ver esta página!</div>
	<?php 
}
