<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <!-- Form Tambah User -->
                <div class="px-6 py-6 mb-5 md:w-1/2 2xl:w-1/3">
                    <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200 mb-4">
                        Tambah User
                    </h2>
                    <form method="POST" action="{{ route('user.store') }}">
                        @csrf
                        <div class="mb-4">
                            <x-text-input id="name" name="name" type="text" class="w-full" placeholder="Name" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-text-input id="email" name="email" type="email" class="w-full" placeholder="Email" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-text-input id="password" name="password" type="password" class="w-full" placeholder="Password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div class="mb-4">
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="w-full" placeholder="Confirm Password" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                        <x-primary-button type="submit">
                            {{ __('Add User') }}
                        </x-primary-button>
                    </form>
                </div>

                <div class="px-6 pt-6 mb-5 md:w-1/2 2xl:w-1/3">
                    @if (request('search'))
                    <h2 class="pb-3 text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                        Search results for : {{ request('search') }}
                    </h2>
                    @endif
                    <form class="flex items-center gap-2">
                        <x-text-input id="search" name="search" type="text" class="w-full"
                            placeholder="Search by name or email ..." value="{{ request('search') }}" autofocus/>
                        <x-primary-button type="submit">
                            {{ __('Search') }}
                        </x-primary-button>
                    </form>
                </div>
                <div class="px-6 text-xl text-gray-900 dark:text-gray-100">
                    <div class="flex items-center justify-between">
                        <div></div>
                        <div>
                            @if (session('success'))
                            <p x-data="{ show: true }" x-show="show" x-transition
                                x-init="setTimeout(() => show = false, 5000)"
                                class="pb-3 text-sm text-green-600 dark:text-green-400">{{ session('success') }}
                            </p>
                            @endif
                            @if (session('danger'))
                            <p x-data="{ show: true }" x-show="show" x-transition
                                x-init="setTimeout(() => show = false, 5000)"
                                class="pb-3 text-sm text-red-600 dark:text-red-400">{{ session('danger') }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="relative overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">
                                    Id
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Name
                                </th>
                                <th scope="col" class="hidden px-6 py-3 md:block">
                                    Email
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Todo
                                </th>
                                <th scope="col" class="px-6 py-3">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr class="odd:bg-white odd:dark:bg-gray-800 even:bg-gray-50 even:dark:bg-gray-700">
                                <td scope="row"
                                    class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    <p>{{ $user->id }}</p>
                                </td>
                                <td class="px-6 py-4 font-medium text-gray-900 md:whitespace-nowrap dark:text-white">
                                    <p>{{ $user->name }}</p>
                                </td>
                                <td class="hidden px-6 py-4 md:block">
                                    <p>{{ $user->email }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p>{{ $user->todos->count() }}
                                        <span>
                                            <span class="text-green-600 dark:text-green-400">({{
                                                $user->todos->where('is_complete', true)->count() }}</span>/
                                            <span
                                                class="text-blue-600 dark:text-blue-400">{{$user->todos->where('is_complete',
                                                false)->count() }})</span>
                                        </span>
                                    </p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex space-x-3">
                                        {{-- Action here --}}
                                        @if ($user->is_admin)
                                        <form action="{{ route('user.removeadmin', $user) }}" method="Post">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                                Remove Admin
                                            </button>
                                        </form>
                                        @else
                                        <form action="{{ route('user.makeadmin', $user) }}" method="Post">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="text-red-600 dark:text-red-400 whitespace-nowrap">
                                                Make Admin
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('user.destroy', $user) }}" method="Post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit"
                                                class="text-red-600 dark:text-red-400 whitespace-nowrap">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>

                            </tr>
                            @empty
                            <tr class="bg-white dark:bg-gray-800">
                                <td scope="row" class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    Empty
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($users->hasPages())
                <div class="p-6">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
