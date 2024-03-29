<?php
class dataface_actions_new_related_record {
	
	function handle(&$params){
		//global $myctr;
		$app =& Dataface_Application::getInstance();
		$query =& $app->getQuery();
		$resultSet =& $app->getResultSet();
        $app->_conf['page_menu_category'] = 'new_related_record_actions_menu';
		
		//$record =& $app->getRecord();	// loads the current record 
		
		import( XFROOT.'Dataface/ShortRelatedRecordForm.php');
		if ( !isset( $query['-relationship'])){
			return PEAR::raiseError(Dataface_LanguageTool::translate(
				'No relationship specified in new related record',
				'No relationship was specified while trying to create new related record.  Please specify a relationship.'
				), DATAFACE_E_ERROR
			);
		}
        
        


		$record = $app->getRecord();
        if ($record) {
            $relationship = $record->_table->getRelationship($query['-relationship']);
            if (PEAR::isError($relationship)) {
                return $relationship;
            }
            $addRecordTable = $relationship->getAddRelatedRecordTable();
            if ($addRecordTable) {
                // This relationship has a "dummy" table registered to be used for adding
                // records to this relationship.
                header('Location: '.$app->url([
                    '-table' => $addRecordTable, 
                    '-action' => 'new',
                    '-add-related-context' => json_encode(['id' => $record->getId(), 'relationship' => $query['-relationship']])]));
                return;
                    
            }
        }
        
		$form = new Dataface_ShortRelatedRecordForm($record, $query['-relationship']);

		$form->_build();
		
		/*
		 *
		 * We need to add the current GET parameter flags (the GET vars starting with '-') so
		 * that the controller knows to pass control to this method again upon form submission.
		 *
		 */
		//$myctr = 0;
		foreach ( $query as $key=>$value){
			//echo "doing $key";
			
			if ( strpos($key,'-')===0 ){
				$form->addElement('hidden', $key);
				
				
				$form->setDefaults( array( $key=>$value) );
				//if ( $myctr == 2 ) exit;
				
				
				
			}
			//$myctr++;
		}
		
		/*
		 * Store the current query string (the portion after the '?') in the form, so we 
		 * can retrieve it after and redirect back to our original location.
		 */
		$form->addElement('hidden', '-query');
		$form->setDefaults( array( '-action'=>$query['-action'],'-query'=>$_SERVER['QUERY_STRING']) );
		
		
		if ( !Dataface_PermissionsTool::checkPermission('add new related record',$form->_record, array('relationship'=>$query['-relationship']))){
			return Dataface_Error::permissionDenied(
				Dataface_LanguageTool::translate(
					'Permission denied while trying to add new related record',
					'Permission Denied: You do not have permission to add related records to the current record.'
				)
			);
			//$this->_vars['error'] =  "<div class=\"error\">Error.  Permission Denied.<!-- At line ".__LINE__." of file ".__FILE__." --></div>";
			//return;
		}
		
		if ( $form->validate() ){
			$vals = $form->exportValues();
				
			$res = $form->process(array(&$form, 'save'), true);

			$response =& Dataface_Application::getResponse();
			
			if ( PEAR::isError($res) && !Dataface_Error::isNotice($res) ){
				return $res;
				//$this->_vars['error'] = "<div class=\"error\">Error.  ".$res->toString()."<!-- At line ".__LINE__." of file ".__FILE__." --></div>";
				//return;
			} else if ( Dataface_Error::isNotice($res) ){
				$success = false;
				$app->addError($res);
				//$response['--msg'] = @$response['--msg'] . "\n".$res->getMessage();
			} else {
				$success = true;
			}
				
			if ( $success ){
				import(XFROOT.'Dataface/Utilities.php');
				Dataface_Utilities::fireEvent('after_action_new_related_record');
				$fquery = array('-action'=>'browse');
				$table = Dataface_Table::loadTable($query['-table']);
                $rel = $table->getRelationship($query['-relationship']);
				$msg = urlencode(
					trim(
						Dataface_LanguageTool::translate(
							"Record successfully added to relationship",
							"Record successfully added to ".$rel->getLabel()." relationship.\n",
							array('relationship'=>$rel->getLabel())
						).
						(isset($response['--msg']) ? $response['--msg'] : '')
					)
				);

			
				foreach ($vals['__keys__'] as $key=>$value){
					$fquery[$key] = "=".$value;
				}
				$fquery['-relationship'] = $query['-relationship'];
				$fquery['-action'] = 'related_records_list';
				$link = Dataface_LinkTool::buildLink($fquery);
				$app->redirect("$link"."&--msg=".$msg);

		 	}
		 }
		 
		ob_start();
		$gdefs = array();
		foreach ( $_GET as $gkey=>$gval ){
			if ( substr($gkey,0, 4) == '--q:' ){
				$gdefs[substr($gkey, 4)] = $gval;
			}
		}
		if ( count($gdefs) > 0 ){
			$form->setDefaults($gdefs);
		}
		
		
		
		
		$form->display();
		$out = ob_get_contents();
		ob_end_clean();
		
		
		$context = array('form'=>$out);
		if ( isset($query['-template']) ) $template = $query['-template'];
		else if ( isset( $params['action']['template']) ) $template = $params['action']['template'];
		else $template = 'Dataface_Add_New_Related_Record.html';
		df_display($context, $template, true);
		
	}
}



?>
