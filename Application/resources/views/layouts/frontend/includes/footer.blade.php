<!-- start footer -->
<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="pull-left">
                    {!! getSetting('FRONTEND_FOOTER') !!}
                </div>
            </div>
            <div class="col-md-6">
                <ul class="pull-right list-inline text-muted">
                    <li><strong><i class="fa fa-envelope"></i> </strong> {{ getSetting('INFO_EMAIL') }}</li>
                    <li><strong><i class="fa fa-phone"></i> </strong>{{ getSetting('SUPPORT_PHONE') }}</li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<!-- end footer -->