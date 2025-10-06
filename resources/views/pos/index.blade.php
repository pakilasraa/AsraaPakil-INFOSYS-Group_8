<x-app-layout>
    <div x-data="pos()" x-init="init()" class="grid grid-cols-1 lg:grid-cols-12 gap-4">
        <!-- Left: Categories (desktop only) -->
        <aside class="hidden lg:block lg:col-span-2 bg-white rounded-lg shadow p-3">
            <h2 class="text-sm font-semibold text-cafe-900 mb-2">Categories</h2>
            @if($categories->count() === 0)
                <div class="text-center py-8 space-y-2">
                    <div class="text-3xl">📂</div>
                    <div class="text-sm text-cafe-700">No categories yet</div>
                </div>
            @else
            <div class="flex flex-col gap-2">
                <button :class="tab===-1 ? 'bg-amber-200 text-cafe-900' : 'bg-[#faf5ef]'" @click="tab=-1" class="px-3 py-2 rounded-md text-left">All</button>
                @foreach($categories as $cat)
                    <button :class="tab==={{ $cat->id }} ? 'bg-amber-200 text-cafe-900' : 'bg-[#faf5ef]'" @click="tab={{ $cat->id }}" class="px-3 py-2 rounded-md text-left">{{ $cat->name }}</button>
                @endforeach
            </div>
            @endif
        </aside>

        <!-- Mobile: Category dropdown -->
        <div class="lg:hidden">
            <div class="bg-white rounded-lg shadow p-3">
                <label class="text-sm text-cafe-900">Category</label>
                @if($categories->count() === 0)
                    <div class="mt-2 text-sm text-cafe-700">No categories yet</div>
                @else
                <select x-model.number="tab" class="w-full mt-1 rounded-lg border input-cafe px-3 py-2">
                    <option value="-1">All</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        </div>

        <!-- Center: Products -->
        <section class="lg:col-span-7 pb-20 md:pb-0">
            @if($products->count() === 0)
                <div class="bg-white rounded-lg shadow p-10 text-center space-y-2">
                    <div class="text-3xl">🍽️</div>
                    <h3 class="text-cafe-900 font-semibold">No products yet</h3>
                    <p class="text-cafe-700 text-sm">Add products from the Products page to begin.</p>
                </div>
            @else
            <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3">
                @foreach($products as $product)
                    <template x-if="tab===-1 || tab==={{ $product->category_id }}">
                        <div class="bg-white rounded-xl shadow overflow-hidden flex flex-col h-full">
                            <div class="bg-[#faf5ef] h-40 md:h-44 xl:h-48">
                                @if($product->image)
                                    <img src="{{ asset('storage/'.$product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-cafe-700">No Image</div>
                                @endif
                            </div>
                            <div class="p-3 flex flex-col gap-2 grow">
                                <div class="font-semibold product-name min-h-[2.5rem]">{{ $product->name }}</div>
                                <div class="text-cafe-900">₱{{ number_format($product->price, 2) }}</div>
                                <button @click="addFromEvent($event)" data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" class="mt-2 w-full px-3 py-2 rounded-md btn-cafe mt-auto">Add</button>
                            </div>
                        </div>
                    </template>
                @endforeach
            </div>
            @endif
        </section>

        <!-- Right: Cart (hidden on mobile, use drawer) -->
        <aside class="hidden md:block lg:col-span-3">
            <div class="bg-white rounded-lg shadow p-3">
                @if (session('status'))
                    <div class="mb-2 rounded-md bg-emerald-100 text-emerald-800 px-3 py-2 text-sm">{{ session('status') }}</div>
                @endif
                <h2 class="text-sm font-semibold text-cafe-900 mb-2">Cart</h2>
                <template x-if="items.length===0">
                    <div class="text-sm text-cafe-700">No items yet.</div>
                </template>
                <div class="space-y-2 max-h-80 overflow-y-auto">
                    <template x-for="(item, idx) in items" :key="item.id">
                        <div class="flex items-center justify-between bg-[#faf5ef] rounded-md px-2 py-2">
                            <div>
                                <div class="text-sm font-medium" x-text="item.name"></div>
                                <div class="text-xs text-cafe-700">₱<span x-text="item.price.toFixed(2)"></span> ×
                                    <input type="number" min="1" class="w-14 rounded border input-cafe px-1 py-0.5" x-model.number="item.qty" @change="recalc()" />
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="font-medium">₱<span x-text="(item.qty*item.price).toFixed(2)"></span></div>
                                <button @click="remove(idx)" class="text-red-600" aria-label="Remove">✖</button>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="mt-3 border-t pt-3">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-cafe-700">Total</div>
                        <div class="text-xl font-semibold text-cafe-900">₱<span x-text="total.toFixed(2)"></span></div>
                    </div>
                    <form method="POST" action="{{ route('pos.store') }}" class="mt-3 space-y-2" @submit="submit($event)">
                        @csrf
                        <input type="hidden" name="items" :value="JSON.stringify(items.map(i=>({product_id:i.id, quantity:i.qty})))">
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="text-sm text-cafe-900">Discount</label>
                                <div class="flex">
                                    <input type="number" step="0.01" min="0" x-model.number="discountValue" class="w-full rounded-l-lg border input-cafe px-3 py-2" placeholder="0" />
                                    <select x-model="discountType" class="rounded-r-lg border border-l-0 input-cafe px-2 select-no-arrow">
                                        <option value="amount">₱</option>
                                        <option value="percent">%</option>
                                    </select>
                                </div>
                            </div>
                            <template x-if="payment==='Cash'">
                                <div>
                                    <label class="text-sm text-cafe-900">Cash</label>
                                    <input type="number" step="0.01" min="0" x-model.number="cash" class="w-full rounded-lg border input-cafe px-3 py-2" placeholder="0" />
                                </div>
                            </template>
                        </div>
                        <div class="text-sm text-cafe-700 flex items-center justify-between">
                            <span>Grand Total</span>
                            <span class="font-semibold text-cafe-900">₱<span x-text="grandTotal().toFixed(2)"></span></span>
                        </div>
                        <template x-if="payment==='Cash'">
                            <div class="text-sm text-cafe-700 flex items-center justify-between">
                                <span>Change</span>
                                <span class="font-semibold text-cafe-900">₱<span x-text="change().toFixed(2)"></span></span>
                            </div>
                        </template>
                        <div>
                            <label class="text-sm text-cafe-900">Payment Method</label>
                            <select name="payment_method" x-model="payment" required class="w-full rounded-lg border input-cafe px-3 py-2">
                                <option value="Cash">Cash</option>
                                <option value="GCash">GCash</option>
                                <option value="Card">Card</option>
                            </select>
                        </div>
                        <input type="hidden" name="discount_type" :value="discountType">
                        <input type="hidden" name="discount_value" :value="discountValue">
                        <input type="hidden" name="cash" :value="cash">
                        <button class="w-full py-3 rounded-xl btn-cafe text-base font-semibold">Complete Sale</button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile cart drawer -->
        <div class="fixed inset-x-0 bottom-0 p-3 md:hidden" x-data="{open:false}" x-on:open-cart.window="open=true">
            <button @click="open=true" class="w-full py-3 rounded-xl btn-cafe">Open Cart (₱<span x-text="total.toFixed(2)"></span>)</button>
            <div x-show="open" class="fixed inset-0 bg-black/40 z-40" @click="open=false"></div>
            <div x-show="open" class="fixed inset-x-0 bottom-0 z-50 bg-white rounded-t-2xl shadow p-4 space-y-3">
                <div class="flex items-center"><div class="font-semibold">Cart</div><button class="ml-auto" @click="open=false">✖</button></div>
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    <template x-for="(item, idx) in items" :key="item.id">
                        <div class="flex items-center justify-between bg-[#faf5ef] rounded-md px-2 py-2">
                            <div>
                                <div class="text-sm font-medium" x-text="item.name"></div>
                                <div class="text-xs text-cafe-700">₱<span x-text="item.price.toFixed(2)"></span> ×
                                    <input type="number" min="1" class="w-14 rounded border input-cafe px-1 py-0.5" x-model.number="item.qty" @change="recalc()" />
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <div class="font-medium">₱<span x-text="(item.qty*item.price).toFixed(2)"></span></div>
                                <button @click="remove(idx)" class="text-red-600" aria-label="Remove">✖</button>
                            </div>
                        </div>
                    </template>
                </div>
                <form method="POST" action="{{ route('pos.store') }}" class="space-y-2" @submit="submit($event)">
                    @csrf
                    <input type="hidden" name="items" :value="JSON.stringify(items.map(i=>({product_id:i.id, quantity:i.qty})))">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-sm text-cafe-900">Discount</label>
                            <div class="flex">
                                <input type="number" step="0.01" min="0" x-model.number="discountValue" class="w-full rounded-l-lg border input-cafe px-3 py-2" placeholder="0" />
                                <select x-model="discountType" class="rounded-r-lg border border-l-0 input-cafe px-2 select-no-arrow">
                                    <option value="amount">₱</option>
                                    <option value="percent">%</option>
                                </select>
                            </div>
                        </div>
                        <template x-if="payment==='Cash'">
                            <div>
                                <label class="text-sm text-cafe-900">Cash</label>
                                <input type="number" step="0.01" min="0" x-model.number="cash" class="w-full rounded-lg border input-cafe px-3 py-2" placeholder="0" />
                            </div>
                        </template>
                    </div>
                    <div class="text-sm text-cafe-700 flex items-center justify-between">
                        <span>Grand Total</span>
                        <span class="font-semibold text-cafe-900">₱<span x-text="grandTotal().toFixed(2)"></span></span>
                    </div>
                    <template x-if="payment==='Cash'">
                        <div class="text-sm text-cafe-700 flex items-center justify-between">
                            <span>Change</span>
                            <span class="font-semibold text-cafe-900">₱<span x-text="change().toFixed(2)"></span></span>
                        </div>
                    </template>
                    <select name="payment_method" x-model="payment" required class="w-full rounded-lg border input-cafe px-3 py-2">
                        <option value="Cash">Cash</option>
                        <option value="GCash">GCash</option>
                        <option value="Card">Card</option>
                    </select>
                    <input type="hidden" name="discount_type" :value="discountType">
                    <input type="hidden" name="discount_value" :value="discountValue">
                    <input type="hidden" name="cash" :value="cash">
                    <button class="w-full py-3 rounded-xl btn-cafe text-base font-semibold">Complete Sale</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function pos(){
        return {
            tab: -1,
            items: [],
            payment: 'Cash',
            total: 0,
            discountType: 'amount',
            discountValue: 0,
            cash: 0,
            init(){ this.recalc(); },
            add(p){
                const found = this.items.find(i=>i.id===p.id);
                if(found){ found.qty++; }
                else { this.items.push({ id:p.id, name:p.name, price:Number(p.price), qty:1 }); }
                this.recalc();
            },
            addFromEvent(e){
                const btn = e.currentTarget;
                const id = Number(btn.dataset.id);
                const name = btn.dataset.name;
                const price = Number(btn.dataset.price);
                this.add({ id, name, price });
            },
            remove(idx){ this.items.splice(idx,1); this.recalc(); },
            recalc(){ this.total = this.items.reduce((s,i)=> s + (i.qty*i.price), 0); },
            discountAmount(){
                if(this.discountType==='percent'){
                    return Math.min(this.total, this.total * (Number(this.discountValue||0)/100));
                }
                return Math.min(this.total, Number(this.discountValue||0));
            },
            grandTotal(){ return Math.max(0, this.total - this.discountAmount()); },
            change(){ return Math.max(0, Number(this.cash||0) - this.grandTotal()); },
            submit(e){
                if(this.items.length===0){ e.preventDefault(); alert('Add items first.'); return; }
                if(this.payment==='Cash' && Number(this.cash||0) < this.grandTotal()){
                    e.preventDefault();
                    alert('Cash provided is less than grand total.');
                }
            }
        }
    }
    </script>
</x-app-layout>


