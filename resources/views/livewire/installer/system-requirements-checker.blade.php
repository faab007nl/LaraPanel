<div>
    <table class="table w-full" wire:ignore>
        <thead>
            <tr>
                <th>Requirement</th>
                <th>Required</th>
                <th>Current</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody data-table-body></tbody>
    </table>

    @script
    <script>
        const questionMarkSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="orange" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-question-mark-icon lucide-circle-question-mark"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>';
        const checkSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="green" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big-icon lucide-circle-check-big"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>';
        const crossSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="red" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-x-icon lucide-circle-x"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>';
        const tableBody = document.querySelector('[data-table-body]');

        document.addEventListener('livewire:mount', () => {
            const requirements = [
                {
                    'id': 'ssh-connection',
                    'name': 'SSH Connection',
                    'required': "Connection to the server via SSH as 'larapanel' user",
                    'currentValueFunction': 'getSshConnectionUser',
                    'statusFunction': 'getSshConnectionStatus'
                },
                {
                    'id': 'php-version',
                    'name': 'PHP Version',
                    'required': '8.1 or higher',
                    'currentValueFunction': 'getPhpVersion',
                    'statusFunction': 'getPhpVersionStatus'
                },
                {
                    'id': 'php-extensions',
                    'name': 'PHP Extensions',
                    'required': '{{ implode(", ", $this->getRequiredPhpExtensions()) }}',
                    'currentValueFunction': 'getCurrentPhpExtensionsText',
                    'statusFunction': 'getPhpExtensionsStatus'
                },
                {
                    'id': 'database',
                    'name': 'Database',
                    'required': 'MySQL 5.7 or higher | MariaDB 10.3 or higher',
                    'currentValueFunction': 'getCurrentDatabaseText',
                    'statusFunction': 'getCurrentDatabaseStatus'
                },
                {
                    'id': 'nginx',
                    'name': 'Nginx',
                    'required': "Enabled and running. Version 1.18 or higher",
                    'currentValueFunction': 'getNginxVersion',
                    'statusFunction': 'getNginxVersionStatus'
                }
            ];
            let extensionStatuses = [];

            requirements.forEach((req => {
                let id = req.id;
                let name = req.name;
                let required = req.required;

                addRow(id, name, required);
                extensionStatuses[id] = 'unknown';

                setTimeout(() => {
                    Livewire.dispatch(`get-requirement`, {
                        'id': id,
                    });
                }, 1000);
                Livewire.on(`update-requirement`, (data) => {
                    let id = data[0]['id'];
                    let currentValue = data[0]['currentValue'];
                    let status = data[0]['status'];

                    updateRow(id, currentValue, status);

                    if (status === 'failed') {
                        extensionStatuses[id] = 'failed';
                    } else if (status === 'passed') {
                        extensionStatuses[id] = 'passed';
                    } else {
                        extensionStatuses[id] = 'unknown';
                    }
                });
            }));

            setInterval(() => {
                let allPassed = true;
                for (let key in extensionStatuses) {
                    if (extensionStatuses[key] !== 'passed') {
                        allPassed = false;
                        break;
                    }
                }

                if (allPassed) {
                    Livewire.dispatch('nextButtonEnable');
                } else {
                    Livewire.dispatch('nextButtonDisable');
                }
            }, 500);
        });

        function addRow(id, name, required, currentValue) {
            const row = document.createElement('tr');
            row.setAttribute('data-row', id);
            row.innerHTML = `
                <th>${name}</th>
                <th>${required}</th>
                <th data-value>Unknown</th>
                <td data-status>${questionMarkSvg}</td>
            `;
            tableBody.appendChild(row);
        }

        function updateRow(id, currentValue, status) {
            const row = document.querySelector(`[data-row="${id}"]`);
            if (row) {
                row.querySelector('[data-value]').textContent = currentValue;

                if(status === "passed"){
                    row.querySelector('[data-status]').innerHTML = checkSvg;
                }
                if(status === "failed"){
                    row.querySelector('[data-status]').innerHTML = crossSvg;
                }
            } else {
                console.warn(`Row with id "${id}" not found.`);
            }
        }

    </script>
    @endscript
</div>
