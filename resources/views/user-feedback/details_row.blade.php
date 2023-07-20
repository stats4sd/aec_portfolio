<div>
    <h3>Message:</h3>
    {{ $entry->message }}
    <br/><br/>


    <h3>Attachments</h3>

    @if($entry->getMedia()->count() === 0)
        There are no attachments to this feedback item.
    @else

        <ul class="list-group w-50" style="min-width: 500px">

        @foreach($entry->getMedia() as $media)
           <li class="list-group-item">
               <a href="{{$media->getUrl()}}">
                   {{ $media->name }}
               </a>
           </li>
        @endforeach
        </ul>

    @endif

</div>
