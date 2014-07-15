 <?php

class nvram {
 
	//----> Clears NVRAM
	public function clear() {
		system_call("nvram -d boot-args");
		return "completed";
	}
}


$nvram = new nvram();


?> 
