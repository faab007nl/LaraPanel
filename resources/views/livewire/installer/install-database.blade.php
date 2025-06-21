<div>
    @if($installStatus === 'installing')
        <div class="card">
            <div class="card-content">
                <div class="media">
                    <div class="media-content">
                        <p class="title is-4">Installing Database</p>
                    </div>
                </div>

                <div class="content">
                    <progress class="progress is-small is-primary" max="100">15%</progress>
                </div>
            </div>
        </div>
    @elseif($installStatus === 'success')
        <div class="notification is-success">
            <h2 class="title is-4">Database Installed Successfully!</h2>
            <p>Your database has been successfully installed. You can now proceed to the next step.</p>
        </div>
    @elseif($installStatus === 'error')
        <div class="notification is-danger">
            <h2 class="title is-4">Installation Error</h2>
            <p>There was an error during the database installation.</p><br>
            <p>{{ $errorMessage }}</p>
        </div>
    @else
        <div class="card">
            <div class="card-content">
                <div class="media">
                    <div class="media-content">
                        <p class="title is-4">Database Installation Pending</p>
                    </div>
                </div>

                <div class="content">
                    <p>Please click the button below to start the database installation process.</p>
                    <button class="button is-primary" wire:click="installDatabase">Install Database</button>
                </div>
            </div>
        </div>
    @endif
</div>
