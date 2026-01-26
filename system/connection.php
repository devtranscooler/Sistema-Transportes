<?php
class MySQL{
	private $conexion; 
	private $total_consultas;

	public function __construct(){ 
	//http://www.php.net/manual/es/mysqlinfo.api.choosing.php
		$ini_array = parse_ini_file('bd.ini');
		
		if(!isset($this->conexion)){
		  	$this->conexion = new mysqli($ini_array["IP"],$ini_array["USR"],$ini_array["PWD"], $ini_array["DB"], $ini_array["PORT"]);
			//$this->conexion = new mysqli('$ini_array["IP"]','livreWeb_dba','kB2h$w^%8exH','livreWeb_db','3306');
		}
	}
		public function getConexion() {
			return $this->conexion;
		}

	public function consulta($consulta){ 
		$this->total_consultas++; 
		$resultado = $this->conexion->query($consulta);
		if(!$resultado){ 
	  		echo mysqli_error($this->conexion);
			echo "<br>". $consulta;
	  		exit;
		}
		return $resultado;
	}

	public function fetch_array($consulta){
		return mysqli_fetch_array($consulta);
	}

	public function fetch_assoc($consulta){
		return mysqli_fetch_assoc($consulta);
	}

	public function num_rows($consulta){
		return mysqli_num_rows($consulta);
	}

	public function getTotalConsultas(){
		return $this->total_consultas; 
	}

	public function getLastId(){
		return mysqli_insert_id($this->conexion); 
	}

	// Método nuevo para evitar inyección SQL
	public function escape_string($valor) {
		return $this->conexion->real_escape_string($valor);
	}
	
}
?>
