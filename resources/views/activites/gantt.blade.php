@extends('layouts.app')
@section('title', 'Gantt — Activités')
@section('page-title', 'Diagramme Gantt — Activités')

@push('styles')
<link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
<style>
    #gantt_here { width: 100%; height: 600px; border-radius: 12px; overflow: hidden; }
    .gantt_task_line { border-radius: 4px; }
</style>
@endpush

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <span class="flex items-center text-xs text-gray-500"><span class="w-3 h-3 rounded bg-indigo-500 mr-1.5 inline-block"></span>À temps</span>
            <span class="flex items-center text-xs text-gray-500"><span class="w-3 h-3 rounded bg-red-500 mr-1.5 inline-block"></span>En retard</span>
        </div>
        <a href="{{ route('activites.index') }}"
           class="text-sm text-indigo-600 hover:underline"><i class="fas fa-list mr-1"></i>Vue liste</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div id="gantt_here"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
<script>
    const ganttData = @json($ganttData);

    gantt.config.date_format  = "%d-%m-%Y";
    gantt.config.scale_unit   = "month";
    gantt.config.date_scale   = "%F %Y";
    gantt.config.subscales    = [{ unit: "week", step: 1, date: "Sem %W" }];
    gantt.config.readonly     = true;
    gantt.config.drag_links   = false;
    gantt.config.columns = [
        { name: "text",     label: "Activité",   width: 280, tree: true },
        { name: "start_date", label: "Début",    width: 90,  align: "center" },
        { name: "end_date",   label: "Fin",      width: 90,  align: "center" },
        {
            name: "progress",
            label: "Avancement",
            width: 80,
            align: "center",
            template: (obj) => Math.round(obj.progress * 100) + "%"
        },
    ];
    gantt.init("gantt_here");
    gantt.parse(ganttData);
</script>
@endpush
