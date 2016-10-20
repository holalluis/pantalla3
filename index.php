<!doctype html><html><head>
	<meta charset=utf-8>
	<TITLE>TARIFA 3.x &mdash; Factura a temps real</TITLE>
	<style>
		*{margin:0}
		body{font-family:Arial}
		.inline{display:inline-block;vertical-align:top}

		/*Animació blinking*/
		@keyframes blink{from{background-color:white}to{background-color:#abc}}
		.blinking{animation:blink 3s ease 0s infinite alternate}

		h1{
			padding:0.3em 0.1em;
			background:#abc;
		}
		form{display:inline;}
		#left,#right{ 
			border:1px solid #ccc; 
			border-top:none;
			border-left:none;
			margin-right:-5px;
			overflow-y:auto;
			padding:0.3em;
			text-align:left;
		}
		#right {width:80%}
		#left  {width:18%}
		#left > span {font-size:13px;background:#ddd;padding:0.1em 0.5em;border-radius:0.3em}
		#left {
			max-height:730px;
			overflow-y:scroll;
		}
		table {display:inline-block;vertical-align:top}
		table {border-collapse:collapse}
		td,th {border:1px solid #ccc;font-weight:normal}
		table input {width:85px}
	</style>

	<!--scripts necessaris per tarifa 3.1-->
	<script src="https://cdn.rawgit.com/holalluis/tarifes/master/bin/tarifa3.js"></script>
	<script src="https://cdn.rawgit.com/holalluis/tarifes/master/bin/classes.js"></script>
	<script src="https://cdn.rawgit.com/holalluis/tarifes/master/bin/funcions.js"></script>

	<script>
	</script>

	<?php
		/**
			entrades: data inici i array de "curva de carga" (kW)
			valors per defecte
		**/
		if(isset($_POST['mes'],$_POST['any']))
		{
			$mes=$_POST['mes'];
			$any=$_POST['any'];
			$inici="$any-$mes-01 00:00";
		}
		else
			$inici=date("Y")."-01-01 00:00";

		$corba=[]; //kW

		//sobreescriu els valors si l'usuari les ha proporcionat
		$corba = isset($_POST['corba']) ? $_POST['corba'] : $corba ; //corba càrrega

		//per si de cas corba és un string buit, fes un array buit
		if($corba=="")$corba=[];

		//passa la corba a javascript: array "energy"
		echo "
		<script>
			var energy=[";
			foreach($corba as $dada) echo "$dada,";
			echo "];
		</script>";
	?>
</head><body onload=init()>

<!--títol-->
<h1 onclick=window.location="index.php" style="cursor:pointer">
	<script>document.write(document.title)</script>
	<!--mostra mes i any de la factura-->
	<span>(<?php echo date("M/Y",strtotime($inici))?>)</span>
</h1><center>

<!--menu el titol-->
<div id=triaMes style="padding:0.5em;">
	<style>
		#triaMes {
			padding:1em;
			position:absolute;
			top:10px;
			right:5px;
		}
		#triaMes input, #triaMes select, #triaMes button {
			font-size:12px;
			vertical-align:top;
			width:60px;
		}
	</style>

	<!--tria mes i any-->
	<form method=POST>
		Mes:
		<select name=mes>
			<option value="01">Gen
			<option value="02">Feb
			<option value="03">Mar
			<option value="04">Abr
			<option value="05">Mai
			<option value="06">Jun
			<option value="07">Jul
			<option value="08">Ago
			<option value="09">Set
			<option value="10">Oct
			<option value="11">Nov
			<option value="12">Des
		</select>
		<input name=any type=number value="<?php if(isset($_POST['any'])){echo $_POST['any'];}else{echo date("Y");}?>">
		<button>ok</button>
		<script>
			var select = document.querySelector("form select[name=mes]")
			<?php
				if(isset($_POST['mes']))
					echo "select.selectedIndex=parseInt($mes)-1;";
			?>
		</script>
	</form>
</div>

<!--main container-->
<div id=main>

	<!--left-->
	<div id=left class=inline>
		<div><b>Corba horària de càrrega:</b></div>
		<span id=count_i>...</span> instants,
		<span id=count_d>...</span> dades
		<ul id=instants>...</ul>
		<style>
			#instants {list-style-type:none}
			#instants li {font-size:11px}
		</style>
	</div>

	<!--right-->
	<div id=right class=inline>
		<table>
			<tr><th colspan=2>
				<th>P1 punta
				<th>P2 llano
				<th>P3 valle
			<tr><th>Potència contractada<th>(kW)
				<td><input id=potConP1 value=300 onchange="potConP1=parseFloat(this.value);init()" type=number min=0>
				<td><input id=potConP2 value=300 onchange="potConP2=parseFloat(this.value);init()" type=number min=0>
				<td><input id=potConP3 value=300 onchange="potConP3=parseFloat(this.value);init()" type=number min=0>
			<tr><th>Preu potència<th>(€/kW)
				<td><input id=eurKWP1 value="59.173468" onchange="eurKWP1=parseFloat(this.value);init()" type=number min=0>
				<td><input id=eurKWP2 value="36.490689" onchange="eurKWP2=parseFloat(this.value);init()" type=number min=0>
				<td><input id=eurKWP3 value="8.3677310" onchange="eurKWP3=parseFloat(this.value);init()" type=number min=0>
			<tr><th>Preu energia<th>(€/kWh)
				<td><input id=eurKWhP1 value="0.014335" onchange="eurKWhP1=parseFloat(this.value);init()" type=number min=0>
				<td><input id=eurKWhP2 value="0.012754" onchange="eurKWhP2=parseFloat(this.value);init()" type=number min=0>
				<td><input id=eurKWhP3 value="0.007805" onchange="eurKWhP3=parseFloat(this.value);init()" type=number min=0>
		</table>

		<table>
			<tr><th>Impost electricitat 1 <td><input id=tax_im1 value=0.04864 onchange="tax_im1=parseFloat(this.value);init()" type=number min=0>
			<tr><th>Impost electricitat 2 <td><input id=tax_im2 value=1.05113 onchange="tax_im2=parseFloat(this.value);init()" type=number min=0>
			<tr><th>IVA                   <td><input id=tax_iva value=0.21    onchange="tax_iva=parseFloat(this.value);init()" type=number min=0>
			<tr><th>Lloguer (€)         <td><input id=tax_alq value=0       onchange="tax_alq=parseFloat(this.value);init()" type=number min=0>
		</table>
		
		<div id=total>
			<style>
				#total {font-size:80px;margin:0.5em 0}
			</style>
		TOTAL: <span id=cost>0</span> €
		</div>

		<!--nova lectura-->
		<div id=nova >
			<style>
				#nova {
					background:#ddd;
					padding:2em 1em;
					border-radius:0.5em;
					font-size:20px;
				}
				#readCorba {
					height:47px;
					vertical-align:top;
					margin-left:-7px;
					padding-left:1em;
					padding-right:1em;
					font-size:22px;
					border-radius:0;
				}
			</style>
			
			<!--corba.txt-->
			<button id=readCorba onclick=readCorba()>Rellegir corba.txt</button>
		</div>
	</div>

</div>

<script>
	tarifa=3;
	tipus.push(new Tipus(3,3,3,3,3,3,3,3,2,2,2,2,2,2,2,2,2,1,1,1,1,1,1,2))//tipus 0
	tipus.push(new Tipus(3,3,3,3,3,3,3,3,2,2,1,1,1,1,1,1,2,2,2,2,2,2,2,2))//tipus 1
	tipus.push(new Tipus(3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,3,2,2,2,2,2,2))//tipus 2 (weekmod)
	weekmod=2 //index del tipus que defineix els caps de setmana i festius
	tint=1 //time interval: tenim una dada de potència (kW) cada hora
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,gen,01)),"Any Nou"			))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,mar,29)),"Divendres Sant"		))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,abr,01)),"Dilluns de Pasqua"	))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,mai,01)),"Dia del Treball"	))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,jun,24)),"Sant Joan"			))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,jul,25)),"Sant Jaume (Girona)"))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,set,11)),"Diada de Catalunya"	))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,oct,12)),"El Pilar"			))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,oct,29)),"Sant Narcís (Girona)"	))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,nov,01)),"Tots Sants"			))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,des,06)),"Dia de la Constitució"	))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,des,25)),"Nadal"				))
	festius.push(new DiaFestiu(new Date(Date.UTC(2016,des,26)),"Sant Esteve"		))

	canvisHoraris.push(new CanviHorari(new Date(Date.UTC(2016,mar,27,02,00)),new Date(Date.UTC(2016,oct,30,02,00))))

	var d = {
		any:"<?php echo substr($inici,0,4)?>",
		mes:parseInt("<?php echo substr($inici,5,2)?>")-1,
		dia:"<?php echo substr($inici,8,2)?>",
	}
	periodes.push(new Periode(
		0,
		new Date(Date.UTC(d.any,d.mes,d.dia)),
		new Date(Date.UTC(d.any,d.mes+1,1)) //fins al dia 1 del mes següent
	));

	//impostos
	tax_im1 = parseFloat(document.querySelector('#tax_im1').value)
	tax_im2 = parseFloat(document.querySelector('#tax_im2').value)
	tax_iva = parseFloat(document.querySelector('#tax_iva').value)
	tax_alq = parseFloat(document.querySelector('#tax_alq').value)

	//potències contractades (kW)
	potConP1 = parseFloat(document.querySelector('#potConP1').value)
	potConP2 = parseFloat(document.querySelector('#potConP2').value)
	potConP3 = parseFloat(document.querySelector('#potConP3').value)
	//preus per kWh per cada període
	eurKWhP1 = parseFloat(document.querySelector('#eurKWhP1').value)
	eurKWhP2 = parseFloat(document.querySelector('#eurKWhP2').value)
	eurKWhP3 = parseFloat(document.querySelector('#eurKWhP3').value)
	//preus per kW per cada període
	eurKWP1 = parseFloat(document.querySelector('#eurKWP1').value)
	eurKWP2 = parseFloat(document.querySelector('#eurKWP2').value)
	eurKWP3 = parseFloat(document.querySelector('#eurKWP3').value)
	//Fi inputs

	function init()
	{
		(function(){
			var d = {
				any:"<?php echo substr($inici,0,4)?>",
				mes:parseInt("<?php echo substr($inici,5,2)?>")-1,
				dia:"<?php echo substr($inici,8,2)?>",
			}
			var inici = new Date(Date.UTC(d.any,d.mes,d.dia));

			var ul = document.querySelector('#main #left #instants')
			ul.innerHTML=""

			var i=0;
			while(true)
			{
				var instant = new Date(inici);
				instant.setHours(inici.getHours()+i);
				if(instant.getUTCMonth()>inici.getUTCMonth()) break;
				if(energy[i]===undefined || energy[i]=="")energy[i]=0;
				var color=energy[i]==0?"#bbb":""
				var li=document.createElement('li')
				ul.appendChild(li)
				li.style.color=color
				li.innerHTML="<span class=data>"+instant.toISOString().replace("T"," ").substr(0,16)+"</span>"
				li.innerHTML+=": <span class=ener>"+energy[i]+"</span> kW"
				i++;
			}

			//solucio cutre per mesos on es canvia l'hora (març i octubre)
			if(d.mes==2)
			{
				while(energy.length!=743) energy.pop()
			}
			else if(d.mes==9)
			{
				while(energy.length!=745) energy.push(0)
			}

			//compta el nombre de dades diferents de zero (dades reals potència)
			var difZero=0;
			for(var i=0;i<energy.length;i++)
			{
				if(energy[i]>0) difZero++;
			}

			//posa els nombres calculats al seu indicador
			document.querySelector("#main #left #count_i").innerHTML=energy.length
			document.querySelector("#main #left #count_d").innerHTML=difZero
		})()
		var cost = calcula()[0];
		document.querySelector('#total #cost').innerHTML=cost.toFixed(2)
		hlAra()
	}

	//busca l'index de la última dada diferent de zero
	function buscaU()
	{
		var ener=document.querySelectorAll('#main #left #instants span.ener')
		for(var i=0;i<ener.length;i++)
		{
			var pot=parseFloat(ener[i].textContent)
			if(pot==0 || pot=="")
				return (i-1)
		}
		return false
	}
	//ressalta en color verd la última dada disponible
	function hlAra()
	{
		var i=buscaU()
		if(!i)return
		var dates = document.querySelectorAll('#main #left #instants span.data')
		try{
			dates[i].parentNode.classList.add("blinking")
		}catch(e){}
	}

	//llegeix l'arxiu "corba.txt"
	function readCorba()
	{
		var rawFile = new XMLHttpRequest();
		rawFile.open("GET","corba.txt",true);
		rawFile.onreadystatechange=function()
		{
			if(rawFile.readyState==4)
			{
				if(rawFile.status==200||rawFile.status==0)
				{
					var allText = rawFile.responseText;
					energy = allText.split("\n");
					init()
				}
			}
		}
		rawFile.send();
	}
	readCorba() //s'executa un cop però no a init!
</script>
