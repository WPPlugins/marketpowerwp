/**
 * Created by RodineMarkPaul on 6/23/2016.
 */
var MultisoftMPPADF = MultisoftMPPADF || {};
MultisoftMPPADF.save_changes = function () {
    var value = {
        "addDistributorConfigFormValues": this.form_config.addDistributorConfigFormValues,
        "addDistributorFormSeparator": this.form_config.addDistributorFormSeparator,
        "addDistributorFormFields": this.form_config.addDistributorFormFields
    };
    jQuery("#multisoft_mpp_adf_form_config_value").val(
        JSON.stringify(value)
    );
    jQuery("#multisoft_mpp_adf_form_config").submit();
};
MultisoftMPPADF.submit_form = function () {

};