<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <title>បញ្ជីឈ្មោះបុគ្គលិក</title>
    <style>
        table { width: 80%; margin: 20px auto; border-collapse: collapse; font-family: Arial, sans-serif; }
        th, td { border: 1px solid #ccc; padding: 12px; text-align: left; }
        th { background-color: #f4f4f4; }
        h2, h3 { text-align: center; margin-top: 30px; }
        .form-container { width: 50%; margin: 20px auto; border: 1px solid #ccc; padding: 20px; border-radius: 5px; font-family: Arial, sans-serif; }
        .form-group { margin-bottom: 15px; }
        .form-group label { font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; margin-top: 5px; box-sizing: border-box; }
        .btn-submit { background-color: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn-submit:hover { background-color: #218838; }
        
        /* CSS សម្រាប់ប៊ូតុង Edit និង Delete */
        .btn-edit { background-color: #ffc107; color: black; padding: 6px 12px; border: none; border-radius: 4px; text-decoration: none; font-size: 14px; margin-right: 5px; cursor: pointer; display: inline-block; }
        .btn-edit:hover { background-color: #e0a800; }
        .btn-delete { background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; font-size: 14px; cursor: pointer; display: inline-block; }
        .btn-delete:hover { background-color: #bd2130; }
        .action-container { display: flex; }
    </style>
</head>
<body>

    <!-- ផ្នែកប្រអប់សម្រាប់បន្ថែម ឬកែប្រែបុគ្គលិក -->
    <div class="form-container">
        <h3>{{ isset($employee_edit) ? 'កែប្រែព័ត៌មានបុគ្គលិក' : 'បន្ថែមបុគ្គលិកថ្មី' }}</h3>
        
        <form action="{{ isset($employee_edit) ? '/employees/update/'.$employee_edit->emp_id : '/employees/store' }}" method="POST">
            @csrf
            <div class="form-group">
                <label>ឈ្មោះបុគ្គលិក៖</label>
                <input type="text" name="emp_name" required value="{{ $employee_edit->emp_name ?? '' }}" placeholder="វាយឈ្មោះទីនេះ">
            </div>
            <div class="form-group">
                <label>តួនាទី (Position)៖</label>
                <input type="text" name="position" value="{{ $employee_edit->position ?? '' }}" placeholder="វាយតួនាទីទីនេះ">
            </div>
            <div class="form-group">
                <label>លេខទូរស័ព្ទ៖</label>
                <input type="text" name="phone" value="{{ $employee_edit->phone ?? '' }}" placeholder="វាយលេខទូរស័ព្ទទីនេះ">
            </div>
            <button type="submit" class="btn-submit">
                {{ isset($employee_edit) ? 'ធ្វើបច្ចុប្បន្នភាពទិន្នន័យ (Update)' : 'រក្សាទុកទិន្នន័យ' }}
            </button>
            
            @if(isset($employee_edit))
                <a href="/employees" style="display: block; text-align: center; margin-top: 10px; color: #6c757d; font-size: 14px;">បោះបង់ការកែប្រែ</a>
                
            @endif
        </form>
    </div>

    <hr style="width: 80%; margin: 40px auto; border: 0; border-top: 1px solid #ccc;">

    <h2>បញ្ជីឈ្មោះបុគ្គលិកនៅក្នុងប្រព័ន្ធ</h2>

    <!-- ផ្នែកតារាងបង្ហាញទិន្នន័យបុគ្គលិក -->
    <table>
        <tr>
            <th>លេខសម្គាល់ (ID)</th>
            <th>ឈ្មោះបុគ្គលិក</th>
            <th>តួនាទី (Position)</th>
            <th>លេខទូរស័ព្ទ</th>
            <th style="width: 150px;">សកម្មភាព (Action)</th> <!-- បន្ថែមជួរនេះ -->
        </tr>
        @forelse($employees as $emp)
        <tr>
            <td>{{ $emp->emp_id }}</td>
            <td>{{ $emp->emp_name }}</td>
            <td>{{ $emp->position ?? 'N/A' }}</td>
            <td>{{ $emp->phone ?? 'N/A' }}</td>
            <td>
                <div class="action-container">
                    <!-- ប៊ូតុងកែប្រែ (Edit) -->
                    <a href="/employees/edit/{{ $emp->emp_id }}" class="btn-edit">កែប្រែ</a>
                    <!-- ប៊ូតុងលុបថ្មី (ជំនួសចូលត្រង់កន្លែង <form> ចាស់) -->
                    <a href="/employees/delete/{{ $emp->emp_id }}" class="btn-delete" onclick="return confirm('តើអ្នកពិតជាចង់លុបបុគ្គលិកនេះមែនទេ?')">លុប</a>

                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="5" style="text-align: center;">មិនមានទិន្នន័យបុគ្គលិកនៅក្នុង Database ឡើយ</td>
        </tr>
        @endforelse
    </table>
</body>
</html>
