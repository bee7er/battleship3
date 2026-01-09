<hr />
<div class="mr-6 bs-sound-checkbox is-pulled-right">
    <label class="checkbox">
        <span class="bs-sound">Sound:</span>
        <input type="radio" id="audio_id_1" name="audio" value="1.0" onclick="onClickSelectAudio(this);" />
        <label class="bs-sound" for="audio_id_1">Full</label>
        <input type="radio" id="audio_id_2" name="audio" value="0.50" onclick="onClickSelectAudio(this);" />
        <label class="bs-sound" for="audio_id_2">50%</label>
        <input type="radio" id="audio_id_3" name="audio" value="0.40" onclick="onClickSelectAudio(this);" checked />
        <label class="bs-sound" for="audio_id_3">40%</label>
        <input type="radio" id="audio_id_4" name="audio" value="0.20" onclick="onClickSelectAudio(this);" />
        <label class="bs-sound" for="audio_id_4">20%</label>
        <input type="radio" id="audio_id_5" name="audio" value="0" onclick="onClickSelectAudio(this);" />
        <label class="bs-sound" for="audio_id_5">Off</label>
        <button class="button bs-test_button" onclick="playGameSound('hit'); return false;">Test</button>
    </label>
</div>

<script type="text/javascript">

    var audioLevel = $('input[name="audio"]:checked').val();
    function playGameSound(sound)
    {
        if (audioLevel > 0.0) {
            playAudio(sound);
        }
    }
    function onClickSelectAudio(elem)
    {
        audioLevel = $(elem).val();
    }

    /**
     * Check for the issuing of a sound
     */
    function checkForSound(locationStatus)
    {
        if ('hit' == locationStatus) {
            playGameSound('hit');
        } else if ('destroyed' == locationStatus) {
            playGameSound('destroyed');
        }
        // else, no sound
    }

    /**
     * Play audio
     */
    function playAudio(sound)
    {
        let audio = new Audio('sounds/' + sound + '.wav');

        if (audioLevel > 0.0) {
            audio.volume = audioLevel;
            audio.play();
        }
    }

</script>
