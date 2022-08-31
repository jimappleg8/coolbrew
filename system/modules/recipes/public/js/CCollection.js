	function CCollection(ReportErrors) {

		//Properties
		this.Collection 	= new Object();	//Object to Associate Keys with Values (including Objects)
		this.Keys 			= new Array();	//List of Keys
		this.ReportErrors 	= ReportErrors;	//Report Errors (T/F)

		CCollection.ERR_DUPLICATE_ITEM 	= " : Item already exists in collection";
		CCollection.ERR_ITEM_NOT_EXIST 	= " : Item does not exist in collection";
		CCollection.ERR_INVALID_KEY = " : Key was invalid";
	
		//Methods
		this.Item 	= _Item;	//Parms(Key/Index) 		-	Returns(Value/Object)
		this.Add 	= _Add;		//Parms(Key, Value) 	-	Returns(Keys Index number)
		this.Exists = _Exists;	//Parms(Key/Index)		-	Returns(Boolean)
		
		this.Remove = _Remove;	//Parms(Key/Index)		-	Returns(none)
		
		this.Count 	= _Count;	//Parms(none)			-	Returns(Number)
		
		this.ReIndex = _ReIndex;//Parms(none)			-	Returns(none)

		//Cookie Compatiable Name Value Pairs
		//this.ReadString = _ReadString;	 //Parms(none)
		//this.WriteString = _WriteString; //Parms(none)
		
		//this.BakeCookie = _BakeCookie;	//Parms(CookieName)	-  Returns(Boolean)
		//this.EatCookie = _EatCookie;	//Parms(CookieName)	-  Returns(Boolean)
		//Private Functions
		}
		function _Item(Key) {

			if (typeof(Key) == "number") {
				//Convert Index to Item
				if (this.Exists(this.Keys[Key])) {
					return this.Collection[this.Keys[Key]];
				}
				else {
					return null;
				}
			}
			else {
				if (this.Exists(Key)) {
					return this.Collection[Key];
				}
				else {
					if (this.ReportErrors) {
						alert(Key + CCollection.ERR_ITEM_NOT_EXIST);
						return null;
					}
					else {
						return null;
					}	
				}
			}
		}

		function _Add(Key, Value) {
			//Check existence first
			if (typeof(Key) != "string") {
				if (this.ReportErrors) {
					alert(Key + CCollection.ERR_INVALID_KEY);
					return null;
				}
				else return null;
			}
			if (Key.length == 0) {
				if (this.ReportErrors) {
					alert(Key + CCollection.ERR_INVALID_KEY);
					return null;
				}
				else return null;
			}
			if (this.Exists(Key)) {
				//Item already exists
				if (this.ReportErrors) {
					alert(Key + CCollection.ERR_DUPLICATE_ITEM);
					return null;
				}
				else return null;
			}
			else {
				//Create and return new Index
				this.Collection[Key] = Value;
				this.Keys[this.Keys.length] = Key
				return this.Keys.length -1;
			}
		}

		function _Exists(Key) {

			if (this.Collection == null) {
				return false;
			}	

			if (typeof(Key) == "number") {
				if (this.Keys[Key]) {
					return (typeof(this.Collection[this.Keys[Key]]) != "undefined");
				}	
			}
			else {
				return (typeof(this.Collection[Key])!= "undefined");
				
			}	
		}

		function _Remove(Key) {

			if (typeof(Key) == "number") {
				if (this.Keys[Key]) {
					//Exists (delete from collection and array)
					//this.Collection[this.Keys[Key]] = null;
					delete this.Collection[this.Keys[Key]];
					this.Keys[Key] = null;
					this.ReIndex();
				}
			}
			else {
				if (this.Exists(Key)) {
					this.Collection[Key] = null;

					//Remove index and ReIndex
					for (i = 0; i < this.Keys.length; i++) {
						if (this.Keys[i] == Key) {
							this.Keys[i] = null;
							break;
						}
					}
					this.ReIndex();
				}
				else {
					if (this.ReportErrors) {
						alert(Key + CCollection.ERR_ITEM_NOT_EXIST);
					}
				}	
			}	
		}

		function _ReIndex() {
		
			var arrTemp = new Array();
			var intItems = 0;
			arrTemp = this.Keys;
			//ReInitialise
			this.Keys = new Array();			
			
			for (i = 0; i < arrTemp.length; i ++) {
				if (arrTemp[i] != null) {
					//Add to array
					this.Keys[intItems ++] = arrTemp[i];
				}
			
			}

		}
		
		function _Count() {
		
			return this.Keys.length;
		
		}
		
	

