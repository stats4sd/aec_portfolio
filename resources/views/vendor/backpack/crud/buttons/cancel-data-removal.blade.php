<a href="{{ url('admin/data-removal/' . $entry->id . '/cancel') }}" class="btn btn-sm btn-link {{ ($entry->status == 'EVERYTHING_REMOVED' || $entry->status == 'CANCELLED') ? 'disabled' : '' }}" data-toggle="popover"><i class="la la-times"></i> Cancel</a>