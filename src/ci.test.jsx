import { describe, expect, it } from 'vitest';
import { render, screen } from "@testing-library/react";

import App from './App.jsx';

describe('App', async () => {
    it('should render while authenticating', () => {
        render(<App />);
        expect(screen.getByText('Vite + Reactoooo')).toBeInTheDocument();
    });
});
