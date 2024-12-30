<div>
    <x-filament-panels::page>
        <x-filament::section >
            <div>

                <div class="flex justify-between xl:gap-60 lg:gap-48 md:gap-16 sm:gap-8 sm:flex-row flex-col gap-4">
                    <div class="w-full">
                        <div>
                            <img src="{{ asset('logo/horon.jpg') }}" alt="" class="w-20 h-28">
                        </div>
                        
                        <div class="flex flex-col">
                            <div class="text-lg font-bold mt-3">
                                Boutique : {{ $this->getRecord()->shop->name }}
                            </div>
                            <div class="text-lg font-bold">
                                Téléphone : +223 90 77 43 46
                            </div>
                            <div class="text-lg font-bold">
                                Adresse :  {{$this->getRecord()->shop->address}}
                            </div>
                            {{-- <div class="text-sm">
                                {{$this->getRecord()->shop->address}}
                            </div> --}}
                            <div class="text-sm">
                                {{-- {{$this->getRecord()->billedFrom->address}} --}}
                            </div>
                            <div class="text-sm">
                                {{-- {{$this->getRecord()->billedFrom->zip}} {{$this->getRecord()->billedFrom->city}} --}}
                            </div>
                            <div class="text-sm">
                                {{-- {{$this->getRecord()->billedFrom->country?->name}} --}}
                            </div>
                        </div>
                        <div class="mt-6">
                            <div class="mt-4">
                                <div class="text-sm text-gray-400">
                                    {{-- bill_to: --}}
                                </div>
                                <div class="text-lg font-bold">
                                    {{-- {{$this->getRecord()->billedFor?->name}} --}}
                                </div>
                                <div class="text-sm">
                                    {{-- {{$this->getRecord()->billedFor?->email}} --}}
                                </div>
                                <div class="text-sm">
                                    {{-- {{$this->getRecord()->billedFor?->phone}} --}}
                                </div>
                                {{-- @php
                                    $address = $this->getRecord()->billedFor?->locations()->first();
                                @endphp --}}
                                {{-- @if($address)
                                    <div class="text-sm">
                                        {{$address->street}}
                                    </div>
                                    <div class="text-sm">
                                        {{$address->zip}}, {{$address->city->name}}
                                    </div>
                                    <div class="text-sm">
                                        {{$this->getRecord()->billedFor?->locations()->first()?->country->name}}
                                    </div>
                                @endif --}}

                            </div>
                        </div>
                    </div>
                    <div class="w-full flex flex-col">
                        <div class="flex justify-end font-bold">
                            <div>
                                <div>
                                    <h1 class="text-3xl uppercase">Borderau De Sortie</h1>
                                </div>
                                <div>
                                    {{-- #{{$this->getRecord()->uuid}} --}}
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end h-full">
                            <div class="flex flex-col justify-end">
                                <div>
                                    <div class="flex mb-2 justify-between gap-4">
                                        <div class="text-gray-400">Ref: </div>
                                        <div>{{ $this->getRecord()->ref }}</div>
                                    </div>
                                    <div class="flex justify-between gap-4">
                                        <div class="text-gray-400">Date sortie: </div>
                                        <div>{{$this->getRecord()->date_out->format('d-m-Y')}}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                   
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg my-4 px-2">
                        
                        <div class="flex flex-col">
                            <div class="flex justify-between  px-4 py-2 border-gray-200 dark:border-gray-700 font-bold border-b text-start">
                                <div class="p-2 w-full">
                                    Quantite
                                </div>
                                <div class="p-2 w-full">
                                    Produit
                                </div>
                                <div class="p-2 w-full">
                                    Prix
                                </div>
                                <div class="p-2 w-full">
                                    Montant
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex flex-col gap-4 divide-y divide-gray-100 dark:divide-white/5">
                            @foreach($this->getRecord()->outItems as $item)
                                <div class="flex justify-between px-4 py-2">
                                    <div class="flex flex-col w-full">
                                        <div class="flex justify-start">
                                            <div>
                                                <div class="font-bold text-lg">
                                                    {{ $item->quantity }}
                                                </div> 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col w-full">
                                        <div class="flex justify-start">
                                            <div>
                                                <div class="font-bold text-lg">
                                                    {{ $item->product->name }} ({{ $item->product->code }})
                                                </div> 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col w-full">
                                        <div class="flex justify-start">
                                            <div>
                                                <div class="font-bold text-lg">
                                                    {{ number_format($item->total / $item->quantity,  0, '.', ' ') }} FR CFA
                                                </div> 
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col w-full">
                                        <div class="flex justify-start">
                                            <div>
                                                <div class="font-bold text-lg">
                                                    {{ number_format($item->total,  0, '.', ' ') }} FR CFA
                                                </div> 
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="w-full">
                                        <div class="p-2">
                                            <div class="flex flex-col mt-2">
                                                <div>
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-400 uppercase w-full">price:</span>
                                                        <span class="w-full">
                                                    {{ number_format($item->price, 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }} </small>
                                                </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-400 uppercase w-full">vat:</span>
                                                        <span class="w-full">
                                                    {{ number_format($item->tax, 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }}</small>
                                                </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-400 uppercase w-full">discount:</span>
                                                        <span class="w-full">
                                                    {{ number_format($item->discount, 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }}</small>
                                                </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-400 uppercase w-full">qty:</span>
                                                        <span class="w-full">
                                                    {{ $item->qty }}
                                                </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="flex justify-between">
                                                        <span class="text-sm text-gray-400 uppercase w-full">total:</span>
                                                        <span class="w-full font-bold">
                                                        {{ number_format($item->total, 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }}</small>
                                                    </span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div> --}}
                                    
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="flex justify-between mt-6">
                        <div class="flex flex-col justify-end gap-4 w-full">
                            {{-- @if($this->getRecord()->is_bank_transfer)
                                <div>
                                    <div class="mb-2 text-xl">
                                        bank_account
                                    </div>
                                    <div class="text-sm flex flex-col">
                                        <div>
                                            <span clas="text-gray-400">name</span> : <span class="font-bold">{{ $this->getRecord()->bank_name }}</span>
                                        </div>
                                        <div>
                                            <span clas="text-gray-400">address</span> : <span class="font-bold">{{ $this->getRecord()->bank_address }}, {{ $this->getRecord()->bank_city }}, {{ $this->getRecord()->bank_country}}</span>
                                        </div>
                                        <div>
                                            <span clas="text-gray-400">branch</span> : <span class="font-bold">{{ $this->getRecord()->bank_branch }}</span>
                                        </div>
                                        <div>
                                            <span clas="text-gray-400">swift</span> : <span class="font-bold">{{ $this->getRecord()->bank_swift }}</span>
                                        </div>
                                        <div>
                                            <span clas="text-gray-400">account</span> : <span class="font-bold">{{ $this->getRecord()->bank_account }}</span>
                                        </div>
                                        <div>
                                            <span clas="text-gray-400">owner'</span> : <span class="font-bold">{{ $this->getRecord()->bank_account_owner }}</span>
                                        </div>
                                        <div>
                                            <span clas="text-gray-400">iban</span> : <span class="font-bold">{{ $this->getRecord()->bank_iban }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif --}}

                            {{-- <div>
                                <div class="mb-2 text-xl">
                                    signature
                                </div>
                                <div class="text-sm text-gray-400">
                                    <div>
                                        {{ $this->getRecord()->billedFrom?->name }}
                                    </div>
                                    <div>
                                        {{ $this->getRecord()->billedFrom?->email }}
                                    </div>
                                    <div>
                                        {{ $this->getRecord()->billedFrom?->phone }}
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <div class="flex flex-col gap-2 mt-4  w-full">
                            <div class="flex justify-between">
                                <div class="font-bold">
                                    Total
                                </div>
                                <div>
                                    {{ number_format($this->getRecord()->amount_total_sale,  0, '.', ' ') }} FR CFA</small>
                                </div>
                            </div>
                            {{-- <div class="flex justify-between">
                                <div class="font-bold">
                                    Benefice
                                </div>
                                <div>
                                    {{ number_format(($this->getRecord()->profit)) }}</small>
                                </div>
                            </div> --}}
                            {{-- <div class="flex justify-between">
                                <div class="font-bold">
                                    subtotal
                                </div>
                                <div>
                                    {{ number_format(($this->getRecord()->total + $this->getRecord()->discount) - ($this->getRecord()->vat + $this->getRecord()->shipping), 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }}</small>
                                </div>
                            </div> --}}
                            {{-- <div class="flex justify-between">
                                <div class="font-bold">
                                    tax
                                </div>
                                <div>
                                    {{ number_format($this->getRecord()->vat, 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }}</small>
                                </div>
                            </div>
                            <div class="flex justify-between">
                                <div class="font-bold">
                                    discount
                                </div>
                                <div>
                                    {{ number_format($this->getRecord()->discount, 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }}</small>
                                </div>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-4">
                                <div class="font-bold">
                                    paid
                                </div>
                                <div>
                                    {{ number_format($this->getRecord()->paid, 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }}</small>
                                </div>
                            </div>
                            <div class="flex justify-between text-xl font-bold">
                                <div>
                                    balance_due
                                </div>
                                <div>
                                    {{ number_format($this->getRecord()->total-$this->getRecord()->paid, 2) }}<small class="text-md font-normal">{{ $this->getRecord()->currency?->iso }}</small>
                                </div>
                            </div> --}}
                        </div>
                    </div>

                    {{-- @if($this->getRecord()->notes)
                        <div class="border-b border-gray-200 dark:border-gray-700 my-4"></div>
                        <div>
                            <div class="mb-2 text-xl">
                                notes
                            </div>
                            <div class="text-sm text-gray-400">
                                {!! $this->getRecord()->notes !!}
                            </div>
                        </div`>
                    @endif --}}

                </div>


            </div>
        </x-filament::section>
        <div class="no-print">
            @php
                $relationManagers = $this->getRelationManagers();
                $hasCombinedRelationManagerTabsWithContent = $this->hasCombinedRelationManagerTabsWithContent();
            @endphp
            @if (count($relationManagers))
                <x-filament-panels::resources.relation-managers
                    :active-locale="isset($activeLocale) ? $activeLocale : null"
                    :active-manager="$this->activeRelationManager ?? ($hasCombinedRelationManagerTabsWithContent ? null : array_key_first($relationManagers))"
                    :content-tab-label="$this->getContentTabLabel()"
                    :content-tab-icon="$this->getContentTabIcon()"
                    :content-tab-position="$this->getContentTabPosition()"
                    :managers="$relationManagers"
                    :owner-record="$record"
                    :page-class="static::class"
                >
                    @if ($hasCombinedRelationManagerTabsWithContent)
                        <x-slot name="content">
                            @if ($this->hasInfolist())
                                {{ $this->infolist }}
                            @else
                                {{ $this->form }}
                            @endif
                        </x-slot>
                    @endif
                </x-filament-panels::resources.relation-managers>
            @endif
        </div>
    </x-filament-panels::page>


    <style type="text/css" media="print">
        .fi-section-content-ctn {
            padding: 0 !important;
            border: none !important;
        }
        .fi-section {
            border: none !important;
            box-shadow: none !important;
        }
        .fi-section-content {
            border: none !important;
            box-shadow: none !important;
        }
        .fi-main {
            margin: 0 !important;
            padding: 0 !important;
            background-color: white !important;
            color: black !important;
        }
        .no-print { display: none; !important; }
        .fi-header { display: none; !important; }
        .fi-topbar { display: none; !important; }
        .fi-sidebar { display: none; !important; }
        .fi-sidebar-close-overlay { display: none; !important; }
    </style>

</div>
