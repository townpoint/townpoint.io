import { startStimulusApp } from '@symfony/stimulus-bridge';
import TextareaAutogrow from 'stimulus-textarea-autogrow'
import ScrollProgress from 'stimulus-scroll-progress'
import Clipboard from 'stimulus-clipboard'
import PasswordVisibility from 'stimulus-password-visibility'

// Registers Stimulus controllers from controllers.json and in the controllers/ directory
export const app = startStimulusApp(require.context(
    '@symfony/stimulus-bridge/lazy-controller-loader!./controllers',
    true,
    /\.[jt]sx?$/
));

// register any custom, 3rd party controllers here
app.register('textarea-autogrow', TextareaAutogrow)
app.register('scroll-progress', ScrollProgress)
app.register('clipboard', Clipboard)
app.register('password-visibility', PasswordVisibility)