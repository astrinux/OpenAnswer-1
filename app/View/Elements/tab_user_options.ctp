    <?php if ($this->Permissions->isAuthorized('DidnumberIndex',$permissions)) { ?> 
        loadPage(this, '/Users' , 'user-content');
    <?php } ?>
    <?php 
            }
            else {
            ?>
            loadPage(this, '/Users/operator/' + myId , 'user-content');				  
            <?php 
                }
            ?>
