/**
 * Created by RodineMarkPaul on 6/26/2016.
 */
var MPPE_CONFIG = parent.MultisoftMPPADF.form_config;
var MPPE_WP = {
    save: function(fields){
        MPPE_CONFIG.addDistributorFormFields = fields;
        parent.MultisoftMPPADF.save_changes();
    },
    submit: function(){
        parent.MultisoftMPPADF.submit_form();
    }
};