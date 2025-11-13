import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RecetaForm } from './receta-form';

describe('RecetaForm', () => {
  let component: RecetaForm;
  let fixture: ComponentFixture<RecetaForm>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RecetaForm]
    })
    .compileComponents();

    fixture = TestBed.createComponent(RecetaForm);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
