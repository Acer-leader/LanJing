                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input-%field_name%">%field_comment%</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="%field_name%" value="{$data['%field_name%']|default=''}" placeholder="%field_comment%" id="input-%field_name%" class="form-control" />
                                        {if condition="isset($message['%field_name%'])"}
                                            <label for="input-%field_name%" class="text-danger">{$message['%field_name%']}</label>
                                        {/if}
                                    </div>
                                </div>

