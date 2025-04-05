<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Audit Log Details</h1>
        <a href="/audit" class="text-indigo-600 hover:text-indigo-900">
            <i class="fas fa-arrow-left mr-1"></i> Back to Audit Trail
        </a>
    </div>

    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h2 class="text-lg font-medium mb-4">Basic Information</h2>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Date & Time</label>
                        <div class="mt-1 text-gray-900">
                            <?php echo date('Y-m-d H:i:s', strtotime($log->created_at)); ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">User</label>
                        <div class="mt-1 text-gray-900">
                            <?php echo htmlspecialchars($log->user_name ?? 'System'); ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Module</label>
                        <div class="mt-1 text-gray-900">
                            <?php echo ucfirst(htmlspecialchars($log->module)); ?>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Action</label>
                        <div class="mt-1 text-gray-900">
                            <?php echo ucfirst(htmlspecialchars($log->action)); ?>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IP Address</label>
                        <div class="mt-1 text-gray-900">
                            <?php echo htmlspecialchars($log->ip_address); ?>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h2 class="text-lg font-medium mb-4">Details</h2>
                    
                    <div class="bg-gray-50 p-4 rounded-md">
                        <pre class="whitespace-pre-wrap text-sm text-gray-700"><?php echo htmlspecialchars($log->details); ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 