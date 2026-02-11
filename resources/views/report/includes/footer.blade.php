<div class="termsandcond" style="direction: rtl">
    @if (isset($settings['invoice_footer']))
    {!! $settings['invoice_footer'] !!}
    @endif
</div>
</div>
<div class="printbutton">
    <a onclick="history.back()" type="button" class="btn btn-secondary"
        style="position: fixed; top: 20px; left: 20px;width: 150px;">{{ __('Back') }}</a>
    <button onclick="window.print()" type="button" class="btn btn-danger"
        style="position: fixed; top: 60px; left: 20px;width: 150px;">{{ __('Print') }}</button>
</div>
</div>
</body>

</html>
