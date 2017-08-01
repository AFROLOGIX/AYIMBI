# AYIMBI
ayimbi resources project

#Install Git

#Clone

  git clone https://github.com/AFROLOGIX/AYIMBI.git

#Import the project
 
  Open the editor (For example Visual Studio Code) 

   Import the project Ayimbi

Open the terminal in the editor (Visual Studio Code) 

   run the command
  
    php artisan serve
 
   launch the applicaton with this address
    
	http://127.0.0.1:8000
   
   
To Push code on the repository (When the modification are ok and well tested without error)
 
 Go to the directory where the project are located
   
   Make a right click and select the option "Git bash here" in the terminal
      
	  #First add the edited files in the local repository with this command
	    
		git add .
		
	  #After commit the changes 
	  
	    git commit -m "Message listing the changes"
		
	  #Always get the last version of the sources before push your code by running this command
	    
		git pull
		
	  #After the pull check if there are no conflicts and then push your editing code in the repository
	  
	    git push
	   

