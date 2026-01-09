@if (count($errors) > 0)
    <div class="container is-fluid">
        <section class="hero">
            <div class="hero-body bs-common-block">
                <p class="bs-errors-title">Something went wrong. Please see the following error(s)</p>
                <div class="fixed-grid has-1-cols">
                    <div class="grid">
                        @foreach ($errors as $error)
                            <div class="cell bs-errors">{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
@endif