<div>
    <table class="min-w-full divide-y divide-gray-300">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                    Title</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                    Description</th>
                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status
                </th>
                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                    <span class="sr-only">Edit</span>
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
            @foreach ($issues as $issue)
                <tr>
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        {{ $issue->title }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        {{ $issue->description }}</td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                        <span
                            class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $issue->status == 'open' ? 'bg-green-100 text-green-800' : ' bg-red-100 text-red-800' }}">{{ ucfirst($issue->status) }}</span>
                    </td>
                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                        <button wire:click="edit({{ $issue->id }})"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-2 rounded">Edit</button>
                        <button wire:click="delete({{ $issue->id }})"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Delete</button>
                    </td>
                </tr>
            @endforeach

            <!-- More people... -->
        </tbody>
    </table>
</div>
