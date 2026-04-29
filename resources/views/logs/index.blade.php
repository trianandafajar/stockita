<x-app-layout title="Activity Logs">

    <div class="space-y-4">

        <div
            class="flex flex-col gap-4 bg-white p-4 rounded-xl shadow-sm border sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                    Activity Logs
                </h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">
                    Riwayat aktivitas sistem
                </p>
            </div>

            <form method="GET" action="{{ route('logs.index') }}">
                <div class="">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="relative w-full sm:w-48">
                            <select name="action"
                                class="w-full appearance-none px-4 py-2 pr-10 border border-gray-200 rounded-lg bg-white focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Action</option>
                                <option value="CREATE" {{ request('action')=='CREATE' ? 'selected' : '' }}>Create
                                </option>
                                <option value="UPDATE" {{ request('action')=='UPDATE' ? 'selected' : '' }}>Update
                                </option>
                                <option value="DELETE" {{ request('action')=='DELETE' ? 'selected' : '' }}>Delete
                                </option>
                            </select>
                        </div>

                        <input type="date" name="date" value="{{ request('date') }}"
                            class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500">

                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Filter
                        </button>

                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">

                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4 text-left font-semibold">Action</th>
                            <th class="px-6 py-4 text-left font-semibold">Model</th>
                            <th class="px-6 py-4 text-left font-semibold">User</th>
                            <th class="px-6 py-4 text-left font-semibold">Tanggal</th>
                            <th class="px-6 py-4 text-right font-semibold">Detail</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span @class([ 'px-2 py-1 text-xs font-medium rounded-lg'
                                    , 'bg-green-100 text-green-700'=> $log->action == 'CREATE',
                                    'bg-yellow-100 text-yellow-700' => $log->action == 'UPDATE',
                                    'bg-red-100 text-red-700' => $log->action == 'DELETE',
                                    'bg-gray-100 text-gray-700' => !in_array($log->action, ['CREATE', 'UPDATE',
                                    'DELETE']),
                                    ])>
                                    {{ $log->action }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-700 whitespace-nowrap">
                                {{ $log->model_type }}
                            </td>

                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap">
                                {{ $log->user->name ?? 'System' }}
                            </td>

                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">
                                {{ $log->created_at->format('d M Y H:i') }}
                            </td>

                            <td x-data class="px-6 py-4 text-right">
                                <button
                                    @click="$dispatch('open-modal', { name: 'log-detail', data: {{ json_encode($log->metadata) }} })"
                                    class="px-3 py-1.5 text-xs font-medium bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition">
                                    Lihat
                                </button>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-gray-400">
                                Tidak ada log
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINATION --}}
        <div>
            {{ $logs->links() }}
        </div>

    </div>


    {{-- MODAL DETAIL --}}
    <x-modal name="log-detail" maxWidth="md">
        <div x-data="{ logData: null }" x-on:open-modal.window="
                if ($event.detail.name === 'log-detail') {
                    logData = $event.detail.data
                }
            " class="p-6">

            <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900">
                    Detail Log
                </h3>

                <button type="button" @click="$dispatch('close-modal', 'log-detail')"
                    class="text-gray-400 hover:text-gray-600 transition">
                    ✕
                </button>
            </div>

            <pre class="bg-gray-100 p-4 rounded text-sm max-h-[300px] overflow-auto"
                x-text="JSON.stringify(logData, null, 2)">
            </pre>

        </div>
    </x-modal>

</x-app-layout>