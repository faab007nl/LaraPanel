@php use App\Enums\RequirementStatus; @endphp
<div>
    <table class="table">
        <thead>
            <tr>
                <th>Requirement</th>
                <th>Required</th>
                <th>Current</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <th>
                    PHP
                </th>
                <th>
                    8.0 or higher
                </th>
                <th>
                    {{ phpversion() }}
                </th>
                <td>
                    {{ $this->getPHPStatus() }}
                </td>
            </tr>

            <tr>
                <th>
                    OpenSSL Extension
                </th>
                <th>
                    Enabled
                </th>
                <th>

                </th>
                <td>
                    {{ $this->getOpenSslStatus() }}
                </td>
            </tr>

            <tr>
                <th>
                    cURL Extension
                </th>
                <th>
                    Enabled
                </th>
                <th>

                </th>
                <td>
                    {{ $this->getCurlStatus() }}
                </td>
            </tr>

            <tr>
                <th>
                    Database
                </th>
                <th>
                    MySQL 5.7 or higher | MariaDB 10.3 or higher
                </th>
                <th>

                </th>
                <td>
                    {{ $this->getMariaDBStatus() }}
                </td>
            </tr>

            <tr>
                <th>
                    OpenSSH
                </th>
                <th>
                    Enabled
                </th>
                <th>

                </th>
                <td>
                    {{ $this->getOpenSshStatus() }}
                </td>
            </tr>

            <tr>
                <th>
                    Nginx
                </th>
                <th>
                    Enabled
                </th>
                <th>

                </th>
                <td>
                    {{ $this->getNginxStatus() }}
                </td>
            </tr>

            <tr>
                <th>
                    LaraPanel User
                </th>
                <th>

                </th>
                <th>
                    Exists
                </th>
                <td>
                    {{ $this->getLaraPanelUserStatus() }}
                </td>
            </tr>

        </tbody>
    </table>
</div>
