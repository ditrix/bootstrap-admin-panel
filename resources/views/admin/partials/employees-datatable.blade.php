<table id="datatablesSimple">
    <thead>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Age</th>
            <th>Start date</th>
            <th>Salary</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Office</th>
            <th>Age</th>
            <th>Start date</th>
            <th>Salary</th>
        </tr>
    </tfoot>
    <tbody>
        @foreach ($employees as $employee)
            <tr>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->position }}</td>
                <td>{{ $employee->office }}</td>
                <td>{{ $employee->age }}</td>
                <td>{{ $employee->start_date->format('Y/m/d') }}</td>
                <td>{{ \App\Helpers\SalaryHelper::formatUsd($employee->salary) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
