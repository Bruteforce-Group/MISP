<div style="display: flex; flex-direction: column; justify-content: center;" class="server-rule-container-pull">
    <?php if ($context == 'servers'): ?>
        <div class="alert alert-primary notice-pull-rule-fetched">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="<?= $this->FontAwesome->getClass('spinner') ?> fa-spin"></i>
            <?= __('Organisation and Tags are being fetched from the remote server.') ?>
        </div>
        <div class="alert alert-success hidden notice-pull-rule-fetched">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= __('Organisation and Tags have been fetched from the remote server.') ?>
        </div>
        <div class="alert alert-warning hidden notice-pull-rule-fetched">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= __('Issues while trying to fetch Organisation and Tags from the remote server.') ?>
            <div><strong><?= __('Reason:') ?></strong></div>
            <div><pre class="reason" style="margin-bottom: 0;"></pre></div>
        </div>
    <?php endif; ?>
    <?php
        echo $this->element('serverRuleElements/rules_widget', [
            'scope' => 'tag',
            'scopeI18n' => __('tag'),
            'technique' => 'pull',
            'allowEmptyOptions' => true,
            'options' => $allTags
        ]);
    ?>

    <div style="display: flex;">
        <h4 class="bold green" style=""><?= __('AND');?></h4>
        <h4 class="bold red" style="margin-left: auto;"><?= __('AND NOT');?></h4>
    </div>

    <?php
        echo $this->element('serverRuleElements/rules_widget', [
            'scope' => 'org',
            'scopeI18n' => __('org'),
            'technique' => 'pull',
            'allowEmptyOptions' => true,
            'options' => $allOrganisations
        ]);
    ?>

    <div style="display: flex;">
        <h4 class="bold green" style=""><?= __('AND');?></h4>
    </div>

    <div style="display: flex; flex-direction: column;">
        <div class="bold green">
            <?= __('Additional sync parameters (based on the event index filters)');?>
        </div>
        <div style="display: flex;">
            <textarea style="width:100%;" placeholder='{"timestamp": "30d"}' type="text" value="" id="urlParams" required="required" data-original-title="" title="" rows="3"></textarea>
        </div>
    </div>
</div>

<script>
$(function() {
    var serverID = "<?= isset($id) ? $id : '' ?>"
    <?php if ($context == 'servers'): ?>
    addPullFilteringRulesToPicker()
    <?php endif; ?>

    function addPullFilteringRulesToPicker() {
        var $rootContainer = $('div.server-rule-container-pull')
        var $pickerTags = $rootContainer.find('select.rules-select-picker-tag')
        var $pickerOrgs = $rootContainer.find('select.rules-select-picker-org')
        if (serverID !== "") {
            $pickerOrgs.parent().children().prop('disabled', true)
            $pickerTags.parent().children().prop('disabled', true)
            getPullFilteringRules(
                function(data) {
                    addOptions($pickerTags, data.tags)
                    addOptions($pickerOrgs, data.organisations)
                    $('div.notice-pull-rule-fetched.alert-success').show()
                },
                function(errorMessage) {
                    showMessage('fail', '<?= __('Could not fetch remote sync filtering rules.') ?>');
                    $('div.notice-pull-rule-fetched.alert-warning').show().find('.reason').text(errorMessage)
                    $pickerTags.parent().remove()
                    $pickerOrgs.parent().remove()
                },
                function() {
                    $('div.notice-pull-rule-fetched.alert-primary').hide()
                    $pickerTags.parent().children().prop('disabled', false).trigger('chosen:updated')
                    $pickerOrgs.parent().children().prop('disabled', false).trigger('chosen:updated')
                },
            )
        } else {
            $('div.notice-pull-rule-fetched.alert-warning').show().find('.reason').text('<?= __('The server must first be saved in order to fetch remote synchronisation rules.') ?>')
            $pickerTags.parent().remove()
            $pickerOrgs.parent().remove()
            $('div.notice-pull-rule-fetched.alert-primary').hide()
        }
    }

    function getPullFilteringRules(cb, fcb, acb) {
        $.getJSON('/servers/queryAvailableSyncFilteringRules/' + serverID, function(availableRules) {
            cb(availableRules)
        })
        .fail(function(jqxhr, textStatus, error) {
            fcb(jqxhr.responseJSON.message !== undefined ? jqxhr.responseJSON.message : textStatus)
        })
        .always(function() {
            acb()
        })
    }

    function addOptions($select, data) {
        data.forEach(function(entry) {
            $select.append($('<option/>', {
                value: entry,
                text : entry
            }));
        });
    }
})
</script>